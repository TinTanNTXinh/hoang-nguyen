<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\PostageServiceInterface;
use App\Repositories\PostageRepositoryInterface;
use App\Repositories\FormulaSampleRepositoryInterface;
use App\Repositories\FormulaRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\UnitRepositoryInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Repositories\OilRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class PostageService implements PostageServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $postageRepo, $formulaSampleRepo, $formulaRepo, $customerRepo, $unitRepo
    , $transportRepo, $oilRepo;

    public function __construct(AuthServiceInterface $authService
        , PostageRepositoryInterface $postageRepo
        , FormulaSampleRepositoryInterface $formulaSampleRepo
        , FormulaRepositoryInterface $formulaRepo
        , CustomerRepositoryInterface $customerRepo
        , UnitRepositoryInterface $unitRepo
        , TransportRepositoryInterface $transportRepo
        , OilRepositoryInterface $oilRepo)
    {
        $this->authService       = $authService;
        $this->postageRepo       = $postageRepo;
        $this->formulaSampleRepo = $formulaSampleRepo;
        $this->formulaRepo       = $formulaRepo;
        $this->customerRepo      = $customerRepo;
        $this->unitRepo          = $unitRepo;
        $this->transportRepo     = $transportRepo;
        $this->oilRepo           = $oilRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'postage';
    }

    public function readAll()
    {
        $all = $this->postageRepo->findAllActiveSkeleton();

        $customers       = $this->customerRepo->findAllActive();
        $units           = $this->unitRepo->findAllActive();
        $formula_samples = $this->formulaSampleRepo->findAllActive();
        $oils            = $this->oilRepo->findAllActiveSkeleton();

        return [
            'postages'        => $all,
            'customers'       => $customers,
            'units'           => $units,
            'formula_samples' => $formula_samples,
            'oils'            => $oils
        ];
    }

    public function readOne($id)
    {
        $one = $this->postageRepo->findOneActiveSkeleton($id);

        $formulas = $this->formulaRepo->findAllActiveByFieldName('postage_id', $id);

        return [
            'postage'  => $one,
            'formulas' => $formulas
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

        $i_postage  = $data['postage'];
        $i_formulas = $data['formulas'];

        try {
            DB::beginTransaction();

            $i_one = [
                'code'             => $this->postageRepo->generateCode('POSTAGE'),
                'unit_price'       => $i_postage['unit_price'],
                'delivery_percent' => $i_postage['delivery_percent'],
                'apply_date'       => DateTimeHelper::toStringDateTimeClientForDB($i_postage['apply_date']),
                'change_by_fuel'   => false,
                'note'             => $i_postage['note'],
                'created_by'       => $this->user->id,
                'updated_by'       => 0,
                'created_date'     => date('Y-m-d H:i:s'),
                'updated_date'     => null,
                'active'           => true,
                'customer_id'      => $i_postage['customer_id'],
                'unit_id'          => $i_postage['unit_id'],
                'type'             => 'OIL',
                'fuel_id'          => $i_postage['fuel_id']
            ];

            $one = $this->postageRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Sort rule
            $i_formulas = $this->sortRule($i_formulas);

            // Insert Formulas
            foreach ($i_formulas as $key => $formula) {
                $i_two = [
                    'code'         => $this->formulaRepo->generateCode('FORMULA'),
                    'rule'         => $formula['rule'],
                    'name'         => $formula['name'],
                    'value1'       => $formula['value1'],
                    'value2'       => $formula['value2'],
                    'index'        => ++$key,
                    'created_by'   => $this->user->id,
                    'updated_by'   => 0,
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => null,
                    'active'       => true,
                    'postage_id'   => $one->id,
                ];

                $two = $this->formulaRepo->createOne($i_two);
                if (!$two) {
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

        $i_postage  = $data['postage'];
        $i_formulas = $data['formulas'];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($i_postage['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->postageRepo->findOneActive($i_postage['id']);

            $i_one = [
                'unit_price'       => $i_postage['unit_price'],
                'delivery_percent' => $i_postage['delivery_percent'],
                'apply_date'       => DateTimeHelper::toStringDateTimeClientForDB($i_postage['apply_date']),
                'change_by_fuel'   => $i_postage['change_by_fuel'],
                'note'             => $i_postage['note'],
                'updated_by'       => $this->user->id,
                'updated_date'     => date('Y-m-d H:i:s'),
                'active'           => true,
                'customer_id'      => $i_postage['customer_id'],
                'unit_id'          => $i_postage['unit_id'],
                'type'             => 'OIL',
                'fuel_id'          => $i_postage['fuel_id']
            ];

            $one = $this->postageRepo->updateOne($one->id, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete Formulas
            $this->formulaRepo->deleteByPostageId($one->id);

            // Sort rule
            $i_formulas = $this->sortRule($i_formulas);

            // Insert Formulas
            foreach ($i_formulas as $key => $formula) {
                $i_two = [
                    'code'         => $this->formulaRepo->generateCode('FORMULA'),
                    'rule'         => $formula['rule'],
                    'name'         => $formula['name'],
                    'value1'       => $formula['value1'],
                    'value2'       => $formula['value2'],
                    'index'        => ++$key,
                    'created_by'   => $this->user->id,
                    'updated_by'   => 0,
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => null,
                    'active'       => true,
                    'postage_id'   => $one->id,
                ];

                $two = $this->formulaRepo->createOne($i_two);
                if (!$two) {
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

            $one = $this->postageRepo->deactivateOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

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

            $one = $this->postageRepo->destroyOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

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

        $filtered = $this->postageRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($customer_id != 0)
            $filtered = $filtered->where('customer_id', $customer_id);

        return [
            'postages' => $filtered->values()
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

        $i_postage  = $data['postage'];
        $i_formulas = $data['formulas'];

//        $skip_id = isset($data['id']) ? [$data['id']] : [];
//
//        if ($data['code'] && $this->userRepo->existsValue('code', $data['code'], $skip_id))
//            array_push($msg_error, 'Mã đã tồn tại.');

//        if ($this->userRepo->existsValue('name', $data['name'], $skip_id))
//            array_push($msg_error, 'Tên đã tồn tại.');

        $postage_apply_date = $i_postage['apply_date'];
        $oil_apply_date     = $this->oilRepo->findOneActive($i_postage['fuel_id'])['apply_date'];

        $compare = DateTimeHelper::compareDateTime($postage_apply_date, DateTimeHelper::$clientFormatDateTime, $oil_apply_date, 'Y-m-d H:i:s');
        if ($compare == 1)
            array_push($msg_error, 'Ngày áp dụng của cước phí phải sau ngày áp dụng giá dầu.');

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

        $transports = $this->transportRepo->findAllActiveByFieldName('postage_id', $id);
        if ($transports->count() > 0) {
            array_push($msg_error, 'Không thể sửa hay xóa cước phí đã có đơn hàng sử dụng.');
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
    public function readByCustomerId($customer_id)
    {
        $postages = $this->postageRepo->findAllActiveSkeleton()->where('customer_id', $customer_id)->values();

        $header_detail = [
            'unit_name'        => ['title' => 'ĐVT', 'data_type' => 'TEXT'],
            'fc_unit_price'    => ['title' => 'Đơn giá', 'data_type' => 'NUMBER', 'prop_name' => 'unit_price'],
            'fd_apply_date'    => ['title' => 'Ngày áp dụng', 'data_type' => 'DATETIME', 'prop_name' => 'apply_date'],
            'delivery_percent' => ['title' => 'Giao xe', 'data_type' => 'NUMBER'],
            'fc_fuel_price'    => ['title' => 'Giá dầu', 'data_type' => 'NUMBER', 'prop_name' => 'fuel_price'],
            'note'             => ['title' => 'Ghi chú', 'data_type' => 'TEXT']
        ];

        $postages->map(function ($postage, $key) use (&$header_detail) {
            $formulas = $this->formulaRepo->findAllActiveByFieldName('postage_id', $postage->id);
            $formulas->each(function ($formula, $key) use ($postage, &$header_detail) {
                switch ($formula->rule) {
                    case 'SINGLE':
                        $postage[$formula->name]       = $formula->value1;
                        $header_detail[$formula->name] = ['title' => $formula->name, 'data_type' => 'TEXT'];
                        break;
                    case 'RANGE':
                    case 'OIL':
                    case 'PAIR':
                        $postage[$formula->name . ' Từ']  = $formula->value1;
                        $postage[$formula->name . ' Đến'] = $formula->value2;

                        $header_detail[$formula->name . ' Từ']  = ['title' => $formula->name . ' Từ', 'data_type' => 'TEXT'];
                        $header_detail[$formula->name . ' Đến'] = ['title' => $formula->name . ' Đến', 'data_type' => 'TEXT'];
                        break;
                }
            });
        });

        return [
            'postages'      => $postages,
            'header_detail' => $header_detail
        ];
    }

    public function sortRule($i_formulas)
    {
        return collect($i_formulas)->sortBy(function ($formula, $key) {
            return $formula['rule'];
        });
    }

}