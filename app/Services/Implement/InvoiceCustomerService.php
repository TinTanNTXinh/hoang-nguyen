<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\InvoiceCustomerServiceInterface;
use App\Repositories\InvoiceCustomerRepositoryInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Repositories\TransportInvoiceRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\InvoiceDetailRepositoryInterface;
use App\Repositories\FileRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class InvoiceCustomerService implements InvoiceCustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $invoiceCustomerRepo, $transportRepo
    , $transportInvoiceRepo, $customerRepo, $invoiceDetailRepo, $fileRepo;

    public function __construct(AuthServiceInterface $authService
        , InvoiceCustomerRepositoryInterface $invoiceCustomerRepo
        , TransportRepositoryInterface $transportRepo
        , TransportInvoiceRepositoryInterface $transportInvoiceRepo
        , CustomerRepositoryInterface $customerRepo
        , InvoiceDetailRepositoryInterface $invoiceDetailRepo
        , FileRepositoryInterface $fileRepo)
    {
        $this->authService          = $authService;
        $this->invoiceCustomerRepo  = $invoiceCustomerRepo;
        $this->transportRepo        = $transportRepo;
        $this->transportInvoiceRepo = $transportInvoiceRepo;
        $this->customerRepo         = $customerRepo;
        $this->invoiceDetailRepo    = $invoiceDetailRepo;
        $this->fileRepo             = $fileRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'invoice_customer';
    }

    public function readAll()
    {
        $all = $this->invoiceCustomerRepo->findAllActiveSkeleton();

        $customers = $this->customerRepo->findAllActive();
        $files     = $this->fileRepo->findAllActive();

        return [
            'invoice_customers' => $all,
            'customers'         => $customers,
            'files'             => $files
        ];
    }

    public function readOne($id)
    {
        $one = $this->invoiceCustomerRepo->findOneActiveSkeleton($id);

        $data_compute = $this->computeByInvoiceId($one->id, false);

        $one->total_exported = $data_compute['total_exported'];

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

        $invoice       = $data['invoice_customer'];
        $transport_ids = $data['transport_ids'];
        try {
            DB::beginTransaction();

            $i_one = [
                'code'  => $this->invoiceCustomerRepo->generateCode('INVOICECUSTOMER'),
                'type1' => $invoice['type1'],
                'type2' => $invoice['type2'],
                'type3' => '',

                'customer_id'   => $invoice['customer_id'],
                'total_revenue' => $invoice['total_revenue'],
                'total_receive' => $invoice['total_receive'],

                'truck_id'                => 0,
                'total_delivery'          => 0,
                'total_cost_in_transport' => 0,
                'total_cost'              => 0,

                'total_pay'  => $invoice['total_pay'],
                'vat'        => $invoice['vat'],
                'after_vat'  => $invoice['after_vat'],
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

            $one = $this->invoiceCustomerRepo->createOne($i_one);

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

            // Insert TransportInvoice
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

            // Update type2 Transport
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

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

        $invoice = $data['invoice_customer'];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($invoice['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->invoiceCustomerRepo->findOneActive($invoice['id']);

            $i_one = [
                'type1' => $invoice['type1'],
                'type2' => $invoice['type2'],

                'customer_id'   => $invoice['customer_id'],
                'total_revenue' => $invoice['total_revenue'],
                'total_receive' => $invoice['total_receive'],

                'total_pay'  => $invoice['total_pay'],
                'vat'        => $invoice['vat'],
                'after_vat'  => $invoice['after_vat'],
                'total_paid' => $one->total_paid + $invoice['paid_amt'],

                'invoice_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['invoice_date'], DateTimeHelper::$clientFormatDate),
                'payment_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['payment_date'], DateTimeHelper::$clientFormatDate),
                'receiver'     => $invoice['receiver'],
                'note'         => $invoice['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->invoiceCustomerRepo->updateOne($one->id, $i_one);

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

            // Update type2 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $one->id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

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

            $one = $this->invoiceCustomerRepo->findOneActive($id);

            $one_deactivated = $this->invoiceCustomerRepo->deactivateOne($id);

            if (!$one_deactivated) {
                DB::rollback();
                return $result;
            }

            // Deactivate InvoiceDetail
            $this->invoiceDetailRepo->deactivateByInvoiceId($id);

            // Remove type2 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

            // Deactivate TransportInvoice
            $this->transportInvoiceRepo->deactivateByInvoiceId($id);

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

            $one = $this->invoiceCustomerRepo->destroyOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete InvoiceDetail
            $this->invoiceDetailRepo->deleteByInvoiceId($id);

            // Remove type2 Transport
            $transport_ids = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

            // Delete TransportInvoice
            $this->transportInvoiceRepo->deleteByInvoiceId($id);

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
        $from_date   = $filter['from_date'];
        $to_date     = $filter['to_date'];
        $range       = $filter['range'];
        $customer_id = $filter['customer_id'];

        $filtered = $this->invoiceCustomerRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        if ($customer_id != 0)
            $filtered = $filtered->where('customer_id', $customer_id);

        return [
            'invoice_customers' => $filtered->values()
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

        $one = $this->invoiceCustomerRepo->findOneActive($id);
        if ($one->total_paid == $one->after_vat) {
            array_push($msg_error, 'Không thể sửa hay xóa hóa đơn hoặc phiếu thanh toán đã trả đủ.');
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
    public function readByCustomerIdAndType2($customer_id, $type2)
    {
        $transports = $this->transportRepo->findByCustomerIdAndType2($customer_id, $type2);

        return [
            'transports' => $transports
        ];
    }

    public function computeByTransportIds($transport_ids)
    {
        $transports = $this->transportRepo->findAllActiveByIds($transport_ids);

        $total_revenue = $transports->sum('revenue');

        $total_receive = $transports->sum('receive');

        $customer_id = $transports->map(function ($item) {
            return $item->customer_id;
        })
            ->unique()
            ->first();

        return [
            'total_revenue'  => $total_revenue,
            'total_receive'  => $total_receive,
            'customer_id'    => $customer_id,
            'total_exported' => 0
        ];
    }

    public function computeByInvoiceId($invoice_id, $validate)
    {
        $msg_error = [];

        // Find One
        $one = $this->invoiceCustomerRepo->findOneActive($invoice_id);

        // Compute data
        $transport_invoices = $this->transportInvoiceRepo->findAllActiveByFieldName('invoice_id', $invoice_id);

        $transport_ids = $transport_invoices->pluck('transport_id')->toArray();

        $data_compute = $this->computeByTransportIds($transport_ids);

        // Recompute total_exported
        $invoice_ids = $this->transportInvoiceRepo->findAllInvoiceIdByInvoiceId($invoice_id);

        $invoices = $this->invoiceCustomerRepo->findAllActiveByIds($invoice_ids);

        $sum_total_pay = $invoices->sum('total_pay');

//        $data_compute['total_exported'] = $data_compute['total_revenue'] - $data_compute['total_receive'] - $total_pay;
        $data_compute['total_exported'] = $sum_total_pay;

        // get type2
        $data_compute['type2'] = $one->type2;

        // Validate
        if ($sum_total_pay == $one->total_revenue - $one->total_receive) {
            array_push($msg_error, 'Hóa đơn hoặc Phiếu thanh toán này đã xuất hết tiền.');
        }

        if ($validate) {
            return [
                'status' => count($msg_error) > 0 ? false : true,
                'errors' => $msg_error,
                'data'   => $data_compute
            ];
        } else {
            return $data_compute;
        }
    }

    public function findType2ByInvoiceId($invoice_id, $type2)
    {
        // neu con HD, PTT thi bang CUSTOMER-HD-NOTFULL, CUSTOMER-PTT-NOTFULL
        // neu het HD, PTT thi bang ''

        // Find One
        $one = $this->invoiceCustomerRepo->findOneActive($invoice_id);

        // Recompute total_exported
        $invoice_ids = $this->transportInvoiceRepo->findAllInvoiceIdByInvoiceId($invoice_id);

        $invoices = $this->invoiceCustomerRepo->findAllActiveByIds($invoice_ids);

        $sum_total_pay = $invoices->sum('total_pay');

        if ($one == null) {
            if ($sum_total_pay == 0) {
                $type2 = '';
            } else {
                $type2 .= '-NOTFULL';
            }
        } else {
            $type2 .= ($sum_total_pay == $one->total_revenue - $one->total_receive) ? '-FULL' : '-NOTFULL';
        }

        return $type2;
    }

    public function readByPaymentDate($payment_date)
    {
        $invoices = $this->invoiceCustomerRepo->findByPaymentDate($payment_date);

        return [
            'invoice_customers' => $invoices
        ];
    }
}