<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\OilServiceInterface;
use App\Repositories\OilRepositoryInterface;
use App\Repositories\FuelCustomerRepositoryInterface;
use App\Repositories\FormulaRepositoryInterface;
use App\Repositories\PostageRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class OilService implements OilServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $oilRepo, $fuelCustomerRepo, $formulaRepo, $postageRepo, $customerRepo;

    public function __construct(AuthServiceInterface $authService
        , OilRepositoryInterface $oilRepo
        , FuelCustomerRepositoryInterface $fuelCustomerRepo
        , FormulaRepositoryInterface $formulaRepo
        , PostageRepositoryInterface $postageRepo
        , CustomerRepositoryInterface $customerRepo)
    {
        $this->authService      = $authService;
        $this->oilRepo          = $oilRepo;
        $this->fuelCustomerRepo = $fuelCustomerRepo;
        $this->formulaRepo      = $formulaRepo;
        $this->postageRepo      = $postageRepo;
        $this->customerRepo     = $customerRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'oil';
    }

    public function readAll()
    {
        $all = $this->oilRepo->findAllActiveSkeleton();

        return [
            'oils' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->oilRepo->findOneActiveSkeleton($id);

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

        try {
            DB::beginTransaction();

            $i_one = [
                'code'         => $this->oilRepo->generateCode('OIL'),
                'price'        => $data['price'],
                'type'         => 'OIL',
                'apply_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['apply_date']),
                'note'         => $data['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->oilRepo->createOne($i_one);

            if (!$one) {
                array_push($result['errors'], 'Thêm giá dầu thất bại.');
                DB::rollback();
                return $result;
            }

            # Insert Postage, Formula, FuelCustomer
            $flag = $this->addPostageByOil($one);
            if (!$flag) {
                array_push($result['errors'], 'Tồn tại cước phí chưa cập nhật ngày áp dụng.');
                DB::rollBack();
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

    public function updateOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->oilRepo->findOneActive($data['id']);

            $i_one = [
                'price'        => $data['price'],
                'type'         => 'OIL',
                'apply_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['apply_date']),
                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->oilRepo->updateOne($one->id, $i_one);

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

    public function deactivateOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->oilRepo->deactivateOne($id);

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

            $one = $this->oilRepo->destroyOne($id);

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

        $filtered = $this->oilRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        return [
            'oils' => $filtered->values()
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
    public function readByApplyDate($apply_date)
    {
        $apply_date = DateTimeHelper::toStringDateTimeClientForDB($apply_date);
        $oil        = $this->oilRepo->findOneActiveByApplyDate($apply_date);
        return [
            'oil' => $oil
        ];
    }

    /**
     * @param $oil \App\Fuel
     * @return boolean
     */
    private function addPostageByOil($oil)
    {
        $fuel_customers = $this->fuelCustomerRepo->findAllActive()->where('type', 'OIL')->values();
        foreach ($fuel_customers as $fuel_customer) {
            # Find Customer
            $customer = $this->customerRepo->findOneActive($fuel_customer->customer_id);

            # Nếu i_apply_date > KH.finish_date -> Bỏ qua
            $compare = DateTimeHelper::compareDateTime($oil->apply_date, 'Y-m-d H:i:s', $customer->finish_date, 'Y-m-d H:i:s');
            if ($compare == -1) continue;

            # Find current Fuel of Customer
            $current_oil_of_customer = $this->oilRepo->findOneActive($fuel_customer->fuel_id);

            # Compute change_percent
            $change_percent = ($oil->price - $current_oil_of_customer->price) / ($current_oil_of_customer->price * 100);

            # Nếu KH không vượt qua limit_oil -> bỏ qua
            if ($customer->limit_oil / 100 > abs($change_percent) * 100) continue;

            # Find Postages of this customer
            $postages = $this->postageRepo->findAllActiveByFieldName('customer_id', $customer->id);

            # Nếu KH chưa có cước phí -> bỏ qua
            if ($postages->count() == 0) continue;

            # Nếu cước phí chưa được cập nhật apply_date -> Báo lỗi
            $check_null = $postages->where('apply_date', null);
            if ($check_null->count() > 0)
                return false;

            # Lấy cước phí theo ngày áp dụng giá dầu
            $max_date = $postages->where('apply_date', '<=', $oil->apply_date)->max('apply_date');
            $postages = $postages->where('apply_date', $max_date);
            foreach ($postages as $postage) {
                $formulas = $this->formulaRepo->findAllActiveByFieldName('postage_id', $postage->id);

                # Nếu trong công thức có Giá dầu -> bỏ qua
                $check_oil = $formulas->where('rule', 'OIL');
                if (count($check_oil) > 0) continue;

                # Insert Postage (apply_date = null)
                $unit_price = $postage->unit_price * abs($change_percent) * $customer->limit_oil / 10000;
                if ($change_percent > 0) {
                    $unit_price = $postage->unit_price + $unit_price;
                    $word       = 'Tăng';
                } else {
                    $unit_price = $postage->unit_price - $unit_price;
                    $word       = 'Giảm';
                }
                $i_postage   = [
                    'code'             => $this->postageRepo->generateCode('POSTAGE'),
                    'unit_price'       => $unit_price,
                    'delivery_percent' => $postage->delivery_percent,
                    'apply_date'       => null,
                    'change_by_fuel'   => true,
                    'note'             => "$word cước vận chuyển và giao xe do giá dầu $word từ " . number_format($current_oil_of_customer->price) . " đến " . number_format($oil->price),
                    'created_by'       => $oil->created_by,
                    'updated_by'       => 0,
                    'created_date'     => $oil->created_date,
                    'updated_date'     => null,
                    'active'           => true,
                    'customer_id'      => $customer->id,
                    'unit_id'          => $postage->unit_id,
                    'type'             => 'OIL',
                    'fuel_id'          => $oil->id
                ];
                $postage_new = $this->postageRepo->createOne($i_postage);

                # Insert Formulas
                foreach ($formulas as $key => $formula) {

                    $i_formula = [
                        'code'         => $this->formulaRepo->generateCode('FORMULA'),
                        'rule'         => $formula->rule,
                        'name'         => $formula->name,
                        'value1'       => $formula->value1,
                        'value2'       => $formula->value2,
                        'index'        => ++$key,
                        'created_by'   => $oil->created_by,
                        'updated_by'   => 0,
                        'created_date' => $oil->created_date,
                        'updated_date' => null,
                        'active'       => true,
                        'postage_id'   => $postage_new->id
                    ];
                    $this->formulaRepo->createOne($i_formula);

                } // END FOREACH Formula

            } // END FOREACH Postage
            # Deactivation Fuel Customer
            $this->fuelCustomerRepo->destroyOne($fuel_customer->id);

            # Insert Fuel Customer
            $i_fuel_customer = [
                'type'         => 'OIL',
                'fuel_id'      => $oil->id,
                'customer_id'  => $customer->id,
                'created_by'   => $oil->created_by,
                'updated_by'   => 0,
                'created_date' => $oil->created_date,
                'updated_date' => null,
                'active'       => true
            ];
            $this->fuelCustomerRepo->createOne($i_fuel_customer);

        } // END FOREACH Fuel Customer
        return true;
    }

}