<?php

namespace App\Services\Eloquent;

use App\Services\TransportServiceInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Repositories\FormulaRepositoryInterface;
use App\Repositories\PostageRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\VoucherRepositoryInterface;
use App\Repositories\TransportFormulaRepositoryInterface;
use App\Repositories\TransportVoucherRepositoryInterface;
use App\Repositories\OilRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class TransportService implements TransportServiceInterface
{
    private $user;
    private $table_name;

    protected $transportRepo, $formulaRepo, $postageRepo, $customerRepo
    , $truckRepo, $productRepo, $voucherRepo, $transportFormulaRepo
    , $transportVoucherRepo, $oilRepo;

    public function __construct(TransportRepositoryInterface $transportRepo
        , FormulaRepositoryInterface $formulaRepo
        , PostageRepositoryInterface $postageRepo
        , CustomerRepositoryInterface $customerRepo
        , TruckRepositoryInterface $truckRepo
        , ProductRepositoryInterface $productRepo
        , VoucherRepositoryInterface $voucherRepo
        , TransportFormulaRepositoryInterface $transportFormulaRepo
        , TransportVoucherRepositoryInterface $transportVoucherRepo
        , OilRepositoryInterface $oilRepo)
    {
        $this->transportRepo        = $transportRepo;
        $this->formulaRepo          = $formulaRepo;
        $this->postageRepo          = $postageRepo;
        $this->customerRepo         = $customerRepo;
        $this->truckRepo            = $truckRepo;
        $this->productRepo          = $productRepo;
        $this->voucherRepo          = $voucherRepo;
        $this->transportFormulaRepo = $transportFormulaRepo;
        $this->transportVoucherRepo = $transportVoucherRepo;
        $this->oilRepo              = $oilRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'transport';
    }

    public function readAll()
    {
        $transports = $this->transportRepo->allSkeleton()->get();

        $customers = $this->customerRepo->allActive();
        $trucks    = $this->truckRepo->allActive();
        $products  = $this->productRepo->allActive();
        $vouchers  = $this->voucherRepo->allActive();

        return [
            'transports' => $transports,
            'customers'  => $customers,
            'trucks'     => $trucks,
            'products'   => $products,
            'vouchers'   => $vouchers
        ];
    }

    public function readOne($id)
    {
        $one = $this->transportRepo->oneSkeleton($id)->first();

        $transport_vouchers = $this->transportVoucherRepo->readByTransportId($id);

        $transport_formulas = $this->transportFormulaRepo->readByTransportId($id);

        return [
            $this->table_name    => $one,
            'transport_vouchers' => $transport_vouchers,
            'transport_formulas' => $transport_formulas
        ];
    }

    public function createOne($data)
    {
        $transport          = $data['transport'];
        $formulas           = $data['formulas'];
        $transport_vouchers = $data['transport_vouchers'];

        try {
            DB::beginTransaction();

            $postage  = $this->postageRepo->find($transport['postage_id']);
            $delivery = $postage->delivery_percent
                * ($transport['revenue'] - ($transport['carrying'] + $transport['parking'] + $transport['fine'] + $transport['phi_tang_bo'] + $transport['add_score']))
                / 100;

            $input = [
                'code'             => $this->transportRepo->generateCode('TRANSPORT'),
                'transport_date'   => DateTimeHelper::toStringDateTimeClientForDB($transport['transport_date']),
                'type1'            => $transport['type1'],
                'type2'            => '',
                'type3'            => '',
                'quantum_product'  => $transport['quantum_product'],
                'revenue'          => $transport['revenue'],
                'profit'           => 0,
                'receive'          => $transport['receive'],
                'delivery'         => $delivery,
                'carrying'         => $transport['carrying'],
                'parking'          => $transport['parking'],
                'fine'             => $transport['fine'],
                'phi_tang_bo'      => $transport['phi_tang_bo'],
                'add_score'        => $transport['add_score'],
                'delivery_real'    => $transport['delivery'],
                'carrying_real'    => $transport['carrying'],
                'parking_real'     => $transport['parking'],
                'fine_real'        => $transport['fine'],
                'phi_tang_bo_real' => $transport['phi_tang_bo'],
                'add_score_real'   => $transport['add_score'],

                'voucher_number'             => $transport['voucher_number'],
                'quantum_product_on_voucher' => $transport['quantum_product_on_voucher'],
                'receiver'                   => $transport['receiver'],

                'note'         => $transport['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true,
                'truck_id'     => $transport['truck_id'],
                'product_id'   => $transport['product_id'],
                'customer_id'  => $transport['customer_id'],
                'postage_id'   => $transport['postage_id'],
                'fuel_id'      => $transport['fuel_id']
            ];

            $one = $this->transportRepo->create($input);

            if (!$one) {
                DB::rollback();
                return false;
            }

            # Insert TransportVoucher
            foreach ($transport_vouchers as $transport_voucher) {
                if ($transport_voucher['quantum'] <= 0) continue;

                $input = [
                    'voucher_id'   => $transport_voucher['voucher_id'],
                    'transport_id' => $one->id,
                    'quantum'      => $transport_voucher['quantum'],
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];

                $voucher_transport_new = $this->transportVoucherRepo->create($input);

                if (!$voucher_transport_new) {
                    DB::rollback();
                    return false;
                }
            }

            # Insert TransportFormula
            foreach ($formulas as $formula) {

                $input = [
                    'rule'         => $formula['rule'],
                    'name'         => $formula['name'],
                    'value1'       => $formula['value1'],
                    'value2'       => $formula['value2'],
                    'active'       => true,
                    'transport_id' => $one->id
                ];

                $transport_formula_new = $this->transportFormulaRepo->create($input);

                if (!$transport_formula_new) {
                    DB::rollback();
                    return false;
                }
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function updateOne($data)
    {
        $transport          = $data['transport'];
        $formulas           = $data['formulas'];
        $transport_vouchers = $data['transport_vouchers'];

        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($transport['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $input = [
                'transport_date'   => DateTimeHelper::toStringDateTimeClientForDB($transport['transport_date']),
                'type1'            => $transport['type1'],
                'type2'            => '',
                'type3'            => '',
                'quantum_product'  => $transport['quantum_product'],
                'revenue'          => $transport['revenue'],
                'profit'           => 0,
                'receive'          => $transport['receive'],
                'delivery'         => $transport['delivery'],
                'carrying'         => $transport['carrying'],
                'parking'          => $transport['parking'],
                'fine'             => $transport['fine'],
                'phi_tang_bo'      => $transport['phi_tang_bo'],
                'add_score'        => $transport['add_score'],
                'delivery_real'    => $transport['delivery'],
                'carrying_real'    => $transport['carrying'],
                'parking_real'     => $transport['parking'],
                'fine_real'        => $transport['fine'],
                'phi_tang_bo_real' => $transport['phi_tang_bo'],
                'add_score_real'   => $transport['add_score'],

                'voucher_number'             => $transport['voucher_number'],
                'quantum_product_on_voucher' => $transport['quantum_product_on_voucher'],
                'receiver'                   => $transport['receiver'],

                'note'         => $transport['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true,
                'truck_id'     => $transport['truck_id'],
                'product_id'   => $transport['product_id'],
                'customer_id'  => $transport['customer_id'],
                'postage_id'   => $transport['postage_id'],
                'fuel_id'      => $transport['fuel_id']
            ];

            $one = $this->transportRepo->find($transport['id']);

            $one = $this->transportRepo->update($one, $input);
            if (!$one) {
                DB::rollBack();
                return $result;
            }

            # Delete TransportVoucher
            $this->transportVoucherRepo->deleteByTransportId($one->id);


            # Insert TransportVoucher
            foreach ($transport_vouchers as $transport_voucher) {
                if ($transport_voucher['quantum'] <= 0) continue;

                $input = [
                    'voucher_id'   => $transport_voucher['voucher_id'],
                    'transport_id' => $one->id,
                    'quantum'      => $transport_voucher['quantum'],
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];

                $voucher_transport_new = $this->transportVoucherRepo->create($input);

                if (!$voucher_transport_new) {
                    DB::rollback();
                    return $result;
                }
            }

            # Delete TransportFormula
            $this->transportFormulaRepo->deleteByTransportId($one->id);

            # Insert TransportFormula
            foreach ($formulas as $formula) {
                $input = [
                    'rule'         => $formula['rule'],
                    'name'         => $formula['name'],
                    'value1'       => $formula['value1'],
                    'value2'       => $formula['value2'],
                    'active'       => true,
                    'transport_id' => $one->id
                ];

                $transport_formula_new = $this->transportFormulaRepo->create($input);

                if (!$transport_formula_new) {
                    DB::rollback();
                    return $result;
                }
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deactivateOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateDeactivateOne($id);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one_deactivated = $this->transportRepo->deactivate($id);

            if (!$one_deactivated) {
                DB::rollBack();
                return $result;
            }

            # Deactivate TransportVoucher
            $this->transportVoucherRepo->deactivateByTransportId($id);

            # Deactivate TransportFormula
            $this->transportFormulaRepo->deactivateByTransportId($id);

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deleteOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateDeleteOne($id);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->transportRepo->destroy($id) ? true : false;
            if (!$one) {
                DB::rollBack();
                return $result;
            }

            # Delete TransportVoucher
            $this->transportVoucherRepo->deleteByTransportId($id);

            # Delete TransportFormula
            $this->transportFormulaRepo->deleteByTransportId($id);

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $transports = $this->transportRepo->allSkeleton();

        $transports = $this->transportRepo->filterFromDateToDate($transports, 'transports.created_date', $from_date, $to_date);

        $transports = $this->transportRepo->filterRangeDate($transports, 'transports.created_date', $range);

        return [
            'transports' => $transports->get()
        ];
    }

    /** ===== MY VALIDATE ===== */
    public function validateUpdateOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    public function validateDeactivateOne($id)
    {
        $msg_error = [];

        $one = $this->transportRepo->find($id);
        if ($one->type2 != '' || $one->type3 != '') {
            array_push($msg_error, 'Không thể sửa hay xóa đơn hàng đã xuất hóa đơn hoặc phiếu thanh toán.');
        }

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    public function validateDeleteOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    /** ===== MY FUNCTION ===== */
    public function readFormulas($data)
    {
        $customer_id    = $data['customer_id'];
        $transport_date = DateTimeHelper::toStringDateTimeClientForDB($data['transport_date']);

        $oil = $this->oilRepo->findByApplyDate($transport_date);

        $postage = $this->postageRepo->findByCustomerIdAndTransportDate($customer_id, $transport_date);

        if (!$postage) return [
            'formulas' => [],
            'oil'      => $oil
        ];

        $formulas = $this->formulaRepo->readByPostageId($postage->id);

        return [
            'formulas' => $formulas,
            'oil'      => $oil
        ];
    }

    public function readPostage($data)
    {
        $i_customer_id    = $data['customer_id'];
        $i_transport_date = DateTimeHelper::toStringDateTimeClientForDB($data['transport_date']);
        $i_formulas       = $data['formulas'];

        $postage_id = $this->formulaRepo->findPostageIdByFormulas($i_formulas, $i_customer_id, $i_transport_date);
        $postage    = $this->postageRepo->oneSkeleton($postage_id)->first();

        return ['postage' => $postage];
    }
}