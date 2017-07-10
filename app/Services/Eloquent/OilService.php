<?php

namespace App\Services\Eloquent;

use App\Services\OilServiceInterface;
use App\Repositories\OilRepositoryInterface;
use App\Repositories\FuelCustomerRepositoryInterface;
use App\Repositories\FormulaRepositoryInterface;
use App\Repositories\PostageRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class OilService implements OilServiceInterface
{
    private $user;
    private $table_name;

    protected $oilRepo, $fuelCustomerRepo, $formulaRepo, $postageRepo, $customerRepo;

    public function __construct(OilRepositoryInterface $oilRepo
        , FuelCustomerRepositoryInterface $fuelCustomerRepo
        , FormulaRepositoryInterface $formulaRepo
        , PostageRepositoryInterface $postageRepo
        , CustomerRepositoryInterface $customerRepo)
    {
        $this->oilRepo          = $oilRepo;
        $this->fuelCustomerRepo = $fuelCustomerRepo;
        $this->formulaRepo      = $formulaRepo;
        $this->postageRepo      = $postageRepo;
        $this->customerRepo     = $customerRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'oil';
    }

    public function readAll()
    {
        $all = $this->oilRepo->allSkeleton()->get();

        return [
            'oils' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->oilRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
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

            $one = $this->oilRepo->create($i_one);

            if (!$one) {
                array_push($result['errors'], 'Thêm giá dầu thất bại.');
                DB::rollback();
                return $result;
            }

            $fuel_customers = $this->fuelCustomerRepo->allActive();
            foreach ($fuel_customers as $fuel_customer) {
                # Find Customer
                $customer = $this->customerRepo->find($fuel_customer->customer_id);
                # Nếu i_apply_date > KH.finish_date -> Bỏ qua
                $compare = DateTimeHelper::compareDateTime($data['apply_date'], 'd/m/Y H:i:s', $customer->finish_date, 'Y-m-d H:i:s');
                if ($compare == 1) continue;
                # Find current Fuel of Customer
                $current_oil_of_customer = $this->oilRepo->find($fuel_customer->fuel_id);
                # Compute change_percent
                $change_percent = ($one->price - $current_oil_of_customer->price) / ($current_oil_of_customer->price * 100);
                # Nếu KH không vượt qua limit_oil -> bỏ qua
                if ($customer->limit_oil / 100 > abs($change_percent) * 100) continue;
                $postages = $this->postageRepo->readByCustomerId($customer->id);
                # Nếu KH chưa có cước phí -> bỏ qua
                if ($postages->count() == 0) continue;
                # Nếu cước phí chưa được cập nhật apply_date -> Báo lỗi
                $check_null = $postages->where('apply_date', null);
                if ($check_null->count() > 0) {
                    array_push($result['errors'], 'Tồn tại cước phí chưa cập nhật ngày áp dụng.');
                    DB::rollback();
                    return $result;
                }
                $max_date = $postages->where('apply_date', '<=', $one->apply_date)->max('apply_date');
                $postages = $postages->where('apply_date', $max_date);
                foreach ($postages as $postage) {
                    $formulas = $this->formulaRepo->readByPostageId($postage->id);
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
                        'note'             => "$word cước vận chuyển và giao xe do giá dầu $word từ " . number_format($current_oil_of_customer->price) . " đến " . number_format($one->price),
                        'created_by'       => $one->created_by,
                        'updated_by'       => 0,
                        'created_date'     => $one->created_date,
                        'updated_date'     => null,
                        'active'           => true,
                        'customer_id'      => $customer->id,
                        'unit_id'          => $postage->unit_id,
                        'fuel_id'          => $one->id
                    ];
                    $postage_new = $this->postageRepo->create($i_postage);

                    # Insert Formulas
                    foreach ($formulas as $key => $formula) {

                        $i_formula = [
                            'code'         => $this->formulaRepo->generateCode('FORMULA'),
                            'rule'         => $formula->rule,
                            'name'         => $formula->name,
                            'value1'       => $formula->value1,
                            'value2'       => $formula->value2,
                            'index'        => ++$key,
                            'created_by'   => $one->created_by,
                            'updated_by'   => 0,
                            'created_date' => $one->created_date,
                            'updated_date' => null,
                            'active'       => true,
                            'postage_id'   => $postage_new->id
                        ];
                        $this->formulaRepo->create($i_formula);

                    } // END FOREACH Formula

                } // END FOREACH Postage
                # Deactivation Fuel Customer
                $this->fuelCustomerRepo->deactivate($fuel_customer->id);

                # Insert Fuel Customer
                $i_fuel_customer = [
                    'fuel_id'      => $one->id,
                    'customer_id'  => $customer->id,
                    'price'        => $one->price,
                    'type'         => 'OIL',
                    'apply_date'   => $one->apply_date,
                    'note'         => '',
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];
                $this->fuelCustomerRepo->create($i_fuel_customer);

            } // END FOREACH Fuel Customer

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
        try {
            DB::beginTransaction();

            $one = $this->oilRepo->find($data['id']);

            $i_one = [
                'price'        => $data['price'],
                'type'         => 'OIL',
                'apply_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['apply_date']),
                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->oilRepo->update($one, $i_one);

            if (!$one) {
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

    public function deactivateOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->oilRepo->deactivate($id);

            if (!$one) {
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

    public function deleteOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->oilRepo->destroy($id);

            if (!$one) {
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

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $filtered = $this->oilRepo->allSkeleton();

        $filtered = $this->oilRepo->filterFromDateToDate($filtered, 'fuels.created_date', $from_date, $to_date);

        $filtered = $this->oilRepo->filterRangeDate($filtered, 'fuels.created_date', $range);

        return [
            'oils' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */
    public function readByApplyDate($apply_date)
    {
        $apply_date = DateTimeHelper::toStringDateTimeClientForDB($apply_date);
        $oil        = $this->oilRepo->findByApplyDate($apply_date);
        return [
            'oil' => $oil
        ];
    }

}