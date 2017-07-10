<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
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
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class TransportService implements TransportServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $transportRepo, $formulaRepo, $postageRepo, $customerRepo
    , $truckRepo, $productRepo, $voucherRepo, $transportFormulaRepo
    , $transportVoucherRepo, $oilRepo;

    public function __construct(AuthServiceInterface $authService
        , TransportRepositoryInterface $transportRepo
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
        $this->authService          = $authService;
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

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'transport';
    }

    public function readAll()
    {
        $transports = $this->transportRepo->findAllActiveSkeleton();

        $customers = $this->customerRepo->findAllActive();
        $trucks    = $this->truckRepo->findAllActive();
        $products  = $this->productRepo->findAllActive();
        $vouchers  = $this->voucherRepo->findAllActive();

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
        $one = $this->transportRepo->findOneActiveSkeleton($id);

        $transport_vouchers = $this->transportVoucherRepo->findAllActiveByFieldName('transport_id', $id);

        $transport_formulas = $this->transportFormulaRepo->findAllActiveByFieldName('transport_id', $id);

        return [
            $this->table_name    => $one,
            'transport_vouchers' => $transport_vouchers,
            'transport_formulas' => $transport_formulas
        ];
    }

    public function createOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        $transport          = $data['transport'];
        $formulas           = $data['formulas'];
        $transport_vouchers = $data['transport_vouchers'];

        try {
            DB::beginTransaction();

            $postage  = $this->postageRepo->findOneActive($transport['postage_id']);
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
                'delivery_real'    => $delivery,
                'carrying_real'    => $transport['carrying'],
                'parking_real'     => $transport['parking'],
                'fine_real'        => $transport['fine'],
                'phi_tang_bo_real' => $transport['phi_tang_bo'],
                'add_score_real'   => $transport['add_score'],

                'voucher_number'             => $transport['voucher_number'],
                'quantum_product_on_voucher' => $transport['quantum_product_on_voucher'],
                'receiver'                   => $transport['receiver'],
                'receive_place'              => $transport['receive_place'],
                'delivery_place'             => $transport['delivery_place'],

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
                'type'         => $transport['fuel_id'] != 0 ? 'OIL' : '',
                'fuel_id'      => $transport['fuel_id']
            ];

            $one = $this->transportRepo->createOne($input);

            if (!$one) {
                DB::rollback();
                return $result;
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

                $voucher_transport_new = $this->transportVoucherRepo->createOne($input);

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

                $transport_formula_new = $this->transportFormulaRepo->createOne($input);

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

    public function updateOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        $transport          = $data['transport'];
        $formulas           = $data['formulas'];
        $transport_vouchers = $data['transport_vouchers'];

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
                'receive_place'              => $transport['receive_place'],
                'delivery_place'             => $transport['delivery_place'],

                'note'         => $transport['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true,
                'truck_id'     => $transport['truck_id'],
                'product_id'   => $transport['product_id'],
                'customer_id'  => $transport['customer_id'],
                'postage_id'   => $transport['postage_id'],
                'type'         => $transport['fuel_id'] != 0 ? 'OIL' : '',
                'fuel_id'      => $transport['fuel_id']
            ];

            $one = $this->transportRepo->findOneActive($transport['id']);

            $one = $this->transportRepo->updateOne($one->id, $input);
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

                $voucher_transport_new = $this->transportVoucherRepo->createOne($input);

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

                $transport_formula_new = $this->transportFormulaRepo->createOne($input);

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

            $one_deactivated = $this->transportRepo->deactivateOne($id);

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

            $one = $this->transportRepo->destroyOne($id) ? true : false;
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
        $from_date   = $filter['from_date'];
        $to_date     = $filter['to_date'];
        $range       = $filter['range'];
        $customer_id = $filter['customer_id'];
        $truck_id    = $filter['truck_id'];

        $filtered = $this->transportRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        if ($customer_id != 0)
            $filtered = $filtered->where('customer_id', $customer_id);

        if ($truck_id != 0)
            $filtered = $filtered->where('truck_id', $truck_id);

        return [
            'transports' => $filtered->values()
        ];
    }

    /** ===== VALIDATE BASIC ===== */
    public function validateInput($data)
    {
        if (!$this->validateEmpty($data))
            return ['status' => false, 'errors' => 'Dữ liệu không hợp lệ.'];

        $msgs = $this->validateLogic($data);
        return $msgs;
    }

    public function validateEmpty($data)
    {
//        if (!$data['name']) return false;
        return true;
    }

    public function validateLogic($data)
    {
        $msg_error = [];

//        $skip_id = isset($data['id']) ? [$data['id']] : [];
//
//        if ($data['code'] && $this->userRepo->existsValue('code', $data['code'], $skip_id))
//            array_push($msg_error, 'Mã đã tồn tại.');

//        if ($this->userRepo->existsValue('name', $data['name'], $skip_id))
//            array_push($msg_error, 'Tên đã tồn tại.');

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    /** ===== VALIDATE ADVANCED ===== */
    public function validateUpdateOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    public function validateDeactivateOne($id)
    {
        $msg_error = [];

        $one = $this->transportRepo->findOneActive($id);
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

        $oil = $this->oilRepo->findOneActiveByApplyDate($transport_date);

        $postage = $this->postageRepo->findByCustomerIdAndTransportDate($customer_id, $transport_date);

        if (!$postage) return [
            'formulas' => [],
            'oil'      => $oil
        ];

        $formulas = $this->formulaRepo->findAllActiveByFieldName('postage_id', $postage->id);

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
        $postage    = $this->postageRepo->findOneActiveSkeleton($postage_id);

        return ['postage' => $postage];
    }
}