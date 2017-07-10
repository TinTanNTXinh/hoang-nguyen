<?php

namespace App\Services\Eloquent;

use App\Services\InvoiceCustomerServiceInterface;
use App\Repositories\InvoiceCustomerRepositoryInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Repositories\TransportInvoiceRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\InvoiceDetailRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class InvoiceCustomerService implements InvoiceCustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $invoiceCustomerRepo, $transportRepo, $transportInvoiceRepo, $customerRepo, $invoiceDetailRepo;

    public function __construct(InvoiceCustomerRepositoryInterface $invoiceCustomerRepo
        , TransportRepositoryInterface $transportRepo
        , TransportInvoiceRepositoryInterface $transportInvoiceRepo
        , CustomerRepositoryInterface $customerRepo
        , InvoiceDetailRepositoryInterface $invoiceDetailRepo)
    {
        $this->invoiceCustomerRepo  = $invoiceCustomerRepo;
        $this->transportRepo        = $transportRepo;
        $this->transportInvoiceRepo = $transportInvoiceRepo;
        $this->customerRepo         = $customerRepo;
        $this->invoiceDetailRepo    = $invoiceDetailRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'invoice_customer';
    }

    public function readAll()
    {
        $all = $this->invoiceCustomerRepo->allSkeleton()->get();

        $customers = $this->customerRepo->allActive();

        return [
            'invoice_customers' => $all,
            'customers'         => $customers
        ];
    }

    public function readOne($id)
    {
        $one = $this->invoiceCustomerRepo->oneSkeleton($id)->first();

        $data_compute = $this->computeByInvoiceId($one->id, false);

        $one->total_exported = $data_compute['total_exported'];

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
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

                'invoice_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['invoice_date'], 'd/m/Y'),
                'payment_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['payment_date'], 'd/m/Y'),
                'receiver'     => $invoice['receiver'] ?? '',
                'note'         => $invoice['note'] ?? '',
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->invoiceCustomerRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
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
            $this->invoiceDetailRepo->create($i_invoice_detail);

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

                $transport_invoice = $this->transportInvoiceRepo->create($i_transport_invoice);
                if (!$transport_invoice) {
                    DB::rollback();
                    return false;
                }
            }

            // Update type2 Transport
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function updateOne($data)
    {
        $invoice = $data['invoice_customer'];
        $result  = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($invoice['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->invoiceCustomerRepo->find($invoice['id']);

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

                'invoice_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['invoice_date'], 'd/m/Y'),
                'payment_date' => DateTimeHelper::toStringDateTimeClientForDB($invoice['payment_date'], 'd/m/Y'),
                'receiver'     => $invoice['receiver'],
                'note'         => $invoice['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->invoiceCustomerRepo->update($one, $i_one);

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
            $this->invoiceDetailRepo->create($i_invoice_detail);

            // Update type2 Transport
            $transport_ids = $this->transportInvoiceRepo->readByInvoiceId($one->id)->pluck('transport_id')->toArray();
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

            $one = $this->invoiceCustomerRepo->find($id);

            $one_deactivated = $this->invoiceCustomerRepo->deactivate($id);

            if (!$one_deactivated) {
                DB::rollback();
                return $result;
            }

            // Deactivate InvoiceDetail
            $this->invoiceDetailRepo->deactivateByInvoiceId($id);

            // Remove type2 Transport
            $transport_ids = $this->transportInvoiceRepo->readByInvoiceId($id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

            // Deactivate TransportInvoice
            $this->transportInvoiceRepo->deactivateByInvoiceId($id);

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

            $one = $this->invoiceCustomerRepo->destroy($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete InvoiceDetail
            $this->invoiceDetailRepo->deleteByInvoiceId($id);

            // Remove type2 Transport
            $transport_ids = $this->transportInvoiceRepo->readByInvoiceId($id)->pluck('transport_id')->toArray();
            $this->transportRepo->updateType2ByIds($transport_ids, $this->findType2ByInvoiceId($one->id, $one->type2));

            // Delete TransportInvoice
            $this->transportInvoiceRepo->deleteByInvoiceId($id);

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

        $filtered = $this->invoiceCustomerRepo->allSkeleton();

        $filtered = $this->invoiceCustomerRepo->filterFromDateToDate($filtered, 'invoices.created_date', $from_date, $to_date);

        $filtered = $this->invoiceCustomerRepo->filterRangeDate($filtered, 'invoices.created_date', $range);

        return [
            'invoice_customers' => $filtered->get()
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

        $one = $this->invoiceCustomerRepo->find($id);
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
        $transports = $this->transportRepo->readByCustomerIdAndType2($customer_id, $type2);

        return [
            'transports' => $transports
        ];
    }

    public function computeByTransportIds($transport_ids)
    {
        $transports = $this->transportRepo->readByIds($transport_ids);

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
        $one = $this->invoiceCustomerRepo->find($invoice_id);

        // Compute data
        $transport_invoices = $this->transportInvoiceRepo->readByInvoiceId($invoice_id);

        $transport_ids = $transport_invoices->pluck('transport_id')->toArray();

        $data_compute = $this->computeByTransportIds($transport_ids);

        // Recompute total_exported
        $invoice_ids = $this->transportInvoiceRepo->findAllInvoiceIdByInvoiceId($invoice_id);

        $invoices = $this->invoiceCustomerRepo->readByInvoiceIds($invoice_ids);

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

        $invoices = $this->invoiceCustomerRepo->readByInvoiceIds($invoice_ids);

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
        $invoices = $this->invoiceCustomerRepo->readByPaymentDate($payment_date);

        return [
            'invoice_customers' => $invoices
        ];
    }
}