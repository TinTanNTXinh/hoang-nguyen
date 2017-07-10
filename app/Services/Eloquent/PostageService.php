<?php

namespace App\Services\Eloquent;

use App\Services\PostageServiceInterface;
use App\Repositories\PostageRepositoryInterface;
use App\Repositories\FormulaSampleRepositoryInterface;
use App\Repositories\FormulaRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\UnitRepositoryInterface;
use App\Repositories\TransportRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class PostageService implements PostageServiceInterface
{
    private $user;
    private $table_name;

    protected $postageRepo, $formulaSampleRepo, $formulaRepo, $customerRepo, $unitRepo
    , $transportRepo;

    public function __construct(PostageRepositoryInterface $postageRepo
        , FormulaSampleRepositoryInterface $formulaSampleRepo
        , FormulaRepositoryInterface $formulaRepo
        , CustomerRepositoryInterface $customerRepo
        , UnitRepositoryInterface $unitRepo
        , TransportRepositoryInterface $transportRepo)
    {
        $this->postageRepo       = $postageRepo;
        $this->formulaSampleRepo = $formulaSampleRepo;
        $this->formulaRepo       = $formulaRepo;
        $this->customerRepo      = $customerRepo;
        $this->unitRepo          = $unitRepo;
        $this->transportRepo     = $transportRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'postage';
    }

    public function readAll()
    {
        $all = $this->postageRepo->allSkeleton()->get();

        $customers       = $this->customerRepo->allActive();
        $units           = $this->unitRepo->allActive();
        $formula_samples = $this->formulaSampleRepo->allActive();

        return [
            'postages'        => $all,
            'customers'       => $customers,
            'units'           => $units,
            'formula_samples' => $formula_samples
        ];
    }

    public function readOne($id)
    {
        $one = $this->postageRepo->oneSkeleton($id)->first();

        $formulas = $this->formulaRepo->readByPostageId($id);

        return [
            'postage'  => $one,
            'formulas' => $formulas
        ];
    }

    public function createOne($data)
    {
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
                'fuel_id'          => $i_postage['fuel_id']
            ];

            $one = $this->postageRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
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

                $two = $this->formulaRepo->create($i_two);
                if (!$two) {
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
        $i_postage  = $data['postage'];
        $i_formulas = $data['formulas'];

        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($i_postage['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->postageRepo->find($i_postage['id']);

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
                'fuel_id'          => $i_postage['fuel_id']
            ];

            $one = $this->postageRepo->update($one, $i_one);

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

                $two = $this->formulaRepo->create($i_two);
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

            $one = $this->postageRepo->deactivate($id);

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

            $one = $this->postageRepo->destroy($id);

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
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $filtered = $this->postageRepo->allSkeleton();

        $filtered = $this->postageRepo->filterFromDateToDate($filtered, 'postages.created_date', $from_date, $to_date);

        $filtered = $this->postageRepo->filterRangeDate($filtered, 'postages.created_date', $range);

        return [
            'postages' => $filtered->get()
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

        $transports = $this->transportRepo->readByPostageId($id);
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
        $postages = $this->postageRepo->readByCustomerId($customer_id);

        $header_detail = [
            'unit_name'        => ['title' => 'ĐVT', 'data_type' => 'TEXT'],
            'fc_unit_price'    => ['title' => 'Đơn giá', 'data_type' => 'NUMBER', 'prop_name' => 'unit_price'],
            'fd_apply_date'    => ['title' => 'Ngày áp dụng', 'data_type' => 'DATETIME', 'prop_name' => 'apply_date'],
            'delivery_percent' => ['title' => 'Giao xe', 'data_type' => 'NUMBER'],
            'note'             => ['title' => 'Ghi chú', 'data_type' => 'TEXT']
        ];

        $postages->map(function ($postage, $key) use (&$header_detail) {
            $formulas = $this->formulaRepo->readByPostageId($postage->id);
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