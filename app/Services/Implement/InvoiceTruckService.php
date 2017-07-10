<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\InvoiceTruckServiceInterface;
use App\Repositories\InvoiceTruckRepositoryInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Repositories\TransportInvoiceRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Repositories\InvoiceDetailRepositoryInterface;
use App\Repositories\CostRepositoryInterface;
use App\Repositories\FileRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class InvoiceTruckService implements InvoiceTruckServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $invoiceTruckRepo, $transportRepo, $transportInvoiceRepo, $truckRepo, $invoiceDetailRepo
    , $costRepo, $fileRepo;

    public function __construct(AuthServiceInterface $authService
        , InvoiceTruckRepositoryInterface $invoiceTruckRepo
        , TransportRepositoryInterface $transportRepo
        , TransportInvoiceRepositoryInterface $transportInvoiceRepo
        , TruckRepositoryInterface $truckRepo
        , InvoiceDetailRepositoryInterface $invoiceDetailRepo
        , CostRepositoryInterface $costRepo
        , FileRepositoryInterface $fileRepo)
    {
        $this->authService          = $authService;
        $this->invoiceTruckRepo     = $invoiceTruckRepo;
        $this->transportRepo        = $transportRepo;
        $this->transportInvoiceRepo = $transportInvoiceRepo;
        $this->truckRepo            = $truckRepo;
        $this->invoiceDetailRepo    = $invoiceDetailRepo;
        $this->costRepo             = $costRepo;
        $this->fileRepo             = $fileRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'invoice_truck';
    }

    public function readAll()
    {
        $all = $this->invoiceTruckRepo->findAllActiveSkeleton();

        $trucks = $this->truckRepo->findAllActiveSkeleton();
        $files  = $this->fileRepo->findAllActive();

        return [
            'invoice_trucks' => $all,
            'trucks'         => $trucks,
            'files'          => $files
        ];
    }

    public function readOne($id)
    {
        $one = $this->invoiceTruckRepo->findOneActiveSkeleton($id);

        return [
            $this->table_name => $one
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

        $invoice       = $data['invoice_truck'];
        $transport_ids = $data['transport_ids'];
        try {
            DB::beginTransaction();

            $i_one = [
                'code'  => $this->invoiceTruckRepo->generateCode('INVOICETRUCK'),
                'type1' => $invoice['type1'],
                'type2' => '',
                'type3' => $invoice['type3'],

                'customer_id'   => 0,
                'total_revenue' => 0,
                'total_receive' => 0,

                'truck_id'                => $invoice['truck_id'],
                'total_delivery'          => $invoice['total_delivery'],
                'total_cost_in_transport' => $invoice['total_cost_in_transport'],
                'total_cost'              => $invoice['total_cost'],

                'total_pay'  => $invoice['total_pay'],
                'vat'        => 0,
                'after_vat'  => $invoice['total_pay'],
                'total_paid' => $invoice['paid_amt'],

                'invoice_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['invoice_date'], DateTimeHelper::$clientFormatDate),
                'payment_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['payment_date'], DateTimeHelper::$clientFormatDate),
                'receiver'     => $invoice['receiver'] ?? '',
                'note'         => $invoice['note'] ?? '',
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->invoiceTruckRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Insert InvoiceDetail
            $i_invoice_detail = [
                'paid_amt'     => $one->total_paid,
                'payment_date' => $one->invoice_date,
                'note'         => '',
                'created_by'   => $one->created_by,
                'updated_by'   => 0,
                'created_date' => $one->created_date,
                'updated_date' => null,
                'active'       => true,
                'invoice_id'   => $one->id
            ];
            $this->invoiceDetailRepo->createOne($i_invoice_detail);

            // Insert TransportInvoice & Update Transport
            foreach ($transport_ids as $transport_id) {
                // Insert TransportInvoice
                $i_transport_invoice = [
                    'transport_id' => $transport_id,
                    'invoice_id'   => $one->id,
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];

                $transport_invoice = $this->transportInvoiceRepo->createOne($i_transport_invoice);
                if (!$transport_invoice) {
                    DB::rollback();
                    return $result;
                }
            }

            // Update type3 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $one->id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType3ByIds($transport_ids, 'TRUCK-PTT-FULL');

            // Update Cost
            $this->costRepo->updateInvoiceIdByTruckId($one->truck_id, $one->id);

            DB::commit();
            $result['id']     = $one->id;
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

        $invoice = $data['invoice_truck'];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($invoice['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->invoiceTruckRepo->findOneActive($invoice['id']);

            $i_one = [
                'type1' => $invoice['type1'],
                'type3' => $invoice['type3'],

                'truck_id'                => $invoice['truck_id'],
                'total_delivery'          => $invoice['total_delivery'],
                'total_cost_in_transport' => $invoice['total_cost_in_transport'],
                'total_cost'              => $invoice['total_cost'],

                'total_pay'  => $invoice['total_pay'],
                'vat'        => 0,
                'after_vat'  => $invoice['total_pay'],
                'total_paid' => $one->total_paid + $invoice['paid_amt'],

                'invoice_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['invoice_date'], DateTimeHelper::$clientFormatDate),
                'payment_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['payment_date'], DateTimeHelper::$clientFormatDate),
                'receiver'     => $invoice['receiver'],
                'note'         => $invoice['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->invoiceTruckRepo->updateOne($one->id, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Insert InvoiceDetail
            $i_invoice_detail = [
                'paid_amt'     => $invoice['paid_amt'],
                'payment_date' => $one->invoice_date,
                'note'         => '',
                'created_by'   => $one->created_by,
                'updated_by'   => 0,
                'created_date' => $one->created_date,
                'updated_date' => null,
                'active'       => true,
                'invoice_id'   => $one->id
            ];
            $this->invoiceDetailRepo->createOne($i_invoice_detail);

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

            $one = $this->invoiceTruckRepo->findOneActive($id);

            $one_deactivated = $this->invoiceTruckRepo->deactivateOne($id);

            if (!$one_deactivated) {
                DB::rollback();
                return $result;
            }

            // Deactivate InvoiceDetail
            $this->invoiceDetailRepo->deactivateByInvoiceId($id);

            // Remove type3 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType3ByIds($transport_ids, '');

            // Deactivate TransportInvoice
            $this->transportInvoiceRepo->deactivateByInvoiceId($id);

            // Remove invoice_id for Cost
            $this->costRepo->updateInvoiceIdByTruckId($one->truck_id, 0);

            // Deactivate File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('invoices', $id);
            $this->fileRepo->deactivateAllByIds($files->pluck('id')->toArray());

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

            $one = $this->invoiceTruckRepo->findOneActive($id);

            $one_deleted = $this->invoiceTruckRepo->destroyOne($id);

            if (!$one_deleted) {
                DB::rollback();
                return $result;
            }

            // Delete InvoiceDetail
            $this->invoiceDetailRepo->deleteByInvoiceId($id);

            // Remove type3 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType3ByIds($transport_ids, '');

            // Delete TransportInvoice
            $this->transportInvoiceRepo->deleteByInvoiceId($id);

            // Remove invoice_id for Cost
            $this->costRepo->updateInvoiceIdByTruckId($one->truck_id, 0);

            // Delete File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('invoices', $id);
            $this->fileRepo->destroyAllByIds($files->pluck('id')->toArray());

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
        $truck_id  = $filter['truck_id'];

        $filtered = $this->invoiceTruckRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        if ($truck_id != 0)
            $filtered = $filtered->where('truck_id', $truck_id);

        return [
            'invoice_trucks' => $filtered->values()
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

        $one = $this->invoiceTruckRepo->findOneActive($id);
        if ($one->total_paid == $one->after_vat) {
            array_push($msg_error, 'Không thể sửa hay xóa phiếu thanh toán đã trả đủ.');
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
    public function readByTruckIdAndType3($truck_id, $type3)
    {
        $transports = $this->transportRepo->findByTruckIdAndType3($truck_id, $type3);

        return [
            'transports' => $transports
        ];
    }

    public function computeByTransportIds($transport_ids)
    {
        $transports = $this->transportRepo->findAllActiveByIds($transport_ids);

        $total_delivery = $transports->sum('delivery_real');

        $total_cost_in_transport = $transports->sum(function ($transport) {
            return $transport->carrying_real + $transport->parking_real + $transport->fine_real
                + $transport->phi_tang_bo_real + $transport->add_score_real;
        });

        $truck_id = $transports->map(function ($item) {
            return $item->truck_id;
        })
            ->unique()
            ->first();

        $costs = $this->costRepo->findAllActiveByFieldName('truck_id', $truck_id);
//        $cost_ids   = $costs->pluck('id');
        $total_cost = $costs->sum('after_vat');

        return [
            'total_delivery'          => $total_delivery,
            'total_cost_in_transport' => $total_cost_in_transport,
            'total_cost'              => $total_cost,
            'truck_id'                => $truck_id
        ];
    }

    public function updateCostInTransport($data)
    {
        $transport_id     = $data['id'];
        $carrying_real    = $data['carrying_real'];
        $parking_real     = $data['parking_real'];
        $fine_real        = $data['fine_real'];
        $phi_tang_bo_real = $data['phi_tang_bo_real'];
        $add_score_real   = $data['add_score_real'];

        try {
            DB::beginTransaction();
            $transport = $this->transportRepo->findOneActive($transport_id);

            $i_transport = [
                'carrying_real'    => $carrying_real,
                'parking_real'     => $parking_real,
                'fine_real'        => $fine_real,
                'phi_tang_bo_real' => $phi_tang_bo_real,
                'add_score_real'   => $add_score_real
            ];

            $transport_updated = $this->transportRepo->updateOne($transport->id, $i_transport);
            if (!$transport_updated) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function readTransportsByTruckId($truck_id)
    {
        return $this->transportRepo->findByTruckIdAndType3($truck_id, ['']);
    }

    public function readByPaymentDate($payment_date)
    {
        $invoices = $this->invoiceTruckRepo->findByPaymentDate($payment_date);

        return [
            'invoice_trucks' => $invoices
        ];
    }

}