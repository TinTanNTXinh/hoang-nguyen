<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\UserServiceInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\PositionRepositoryInterface;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\GroupRoleRepositoryInterface;
use App\Repositories\UserRoleRepositoryInterface;
use App\Repositories\UserPositionRepositoryInterface;
use App\Repositories\FieldRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use App\Common\Helpers\HashHelper;
use DB;
use League\Flysystem\Exception;

class UserService implements UserServiceInterface
{
    private $user;
    private $table_name;
    private $fake_pwd;

    protected $authService, $userRepo, $positionRepo, $roleRepo, $groupRoleRepo
    , $userRoleRepo, $userPositionRepo, $fieldRepo;

    public function __construct(AuthServiceInterface $authService
        , UserRepositoryInterface $userRepo
        , PositionRepositoryInterface $positionRepo
        , RoleRepositoryInterface $roleRepo
        , GroupRoleRepositoryInterface $groupRoleRepo
        , UserRoleRepositoryInterface $userRoleRepo
        , UserPositionRepositoryInterface $userPositionRepo
        , FieldRepositoryInterface $fieldRepo)
    {
        $this->authService      = $authService;
        $this->userRepo         = $userRepo;
        $this->positionRepo     = $positionRepo;
        $this->roleRepo         = $roleRepo;
        $this->groupRoleRepo    = $groupRoleRepo;
        $this->userRoleRepo     = $userRoleRepo;
        $this->userPositionRepo = $userPositionRepo;
        $this->fieldRepo        = $fieldRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'user';

        $this->fake_pwd = substr(config('app.key'), 10);
    }

    public function readAll()
    {
        $all = $this->userRepo->findAllActiveSkeleton();

        $positions   = $this->positionRepo->findAllActive();
        $roles       = $this->roleRepo->findAllActive();
        $group_roles = $this->groupRoleRepo->findAllActive();

        return [
            'users'       => $all,
            'positions'   => $positions,
            'roles'       => $roles,
            'group_roles' => $group_roles,
            'fake_pwd'    => $this->fake_pwd
        ];
    }

    public function readOne($id)
    {
        $one = $this->userRepo->findOneActiveSkeleton($id);

        $user_roles     = $this->userRoleRepo->findAllActiveByFieldName('user_id', $one->id)->pluck('role_id');
        $user_positions = $this->userPositionRepo->findAllActiveByFieldName('user_id', $one->id)->pluck('position_id');

        return [
            $this->table_name => $one,
            'user_roles'      => $user_roles,
            'user_positions'  => $user_positions
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

        $i_user           = $data['user'];
        $i_user_roles     = $data['user_roles'];
        $i_user_positions = $data['user_positions'];
//        $i_field          = $data['field'];

        try {
            DB::beginTransaction();

            $i_one = [
                'code'         => $this->userRepo->generateCode('USER'),
                'fullname'     => $i_user['fullname'],
                'username'     => $i_user['username'],
                'password'     => $i_user['password'],
                'address'      => $i_user['address'],
                'phone'        => $i_user['phone'],
                'birthday'     => DateTimeHelper::toStringDateTimeClientForDB($i_user['birthday'], DateTimeHelper::$clientFormatDate),
                'sex'          => $i_user['sex'],
                'email'        => $i_user['email'],
                'note'         => $i_user['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d H:i:s'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->userRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Insert UserRole
            foreach ($i_user_roles as $i_user_role) {
                $i_two = [
                    'user_id'      => $one->id,
                    'role_id'      => $i_user_role,
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];
                $two   = $this->userRoleRepo->createOne($i_two);

                if (!$two) {
                    DB::rollback();
                    return $result;
                }
            }

            // Insert UserPosition
            foreach ($i_user_positions as $i_user_position) {
                $i_three = [
                    'user_id'      => $one->id,
                    'position_id'  => $i_user_position,
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];
                $three   = $this->userPositionRepo->createOne($i_three);

                if (!$three) {
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

        $i_user           = $data['user'];
        $i_user_roles     = $data['user_roles'];
        $i_user_positions = $data['user_positions'];
//        $i_field          = $data['field'];

        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($i_user['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->userRepo->findOneActive($i_user['id']);

            $i_one = [
                'fullname'     => $i_user['fullname'],
                'username'     => $i_user['username'],
                'address'      => $i_user['address'],
                'phone'        => $i_user['phone'],
                'birthday'     => DateTimeHelper::toStringDateTimeClientForDB($i_user['birthday'], DateTimeHelper::$clientFormatDate),
                'sex'          => $i_user['sex'],
                'email'        => $i_user['email'],
                'note'         => $i_user['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d H:i:s'),
                'active'       => true
            ];

            if ($i_user['password'] != $this->fake_pwd)
                $i_one['password'] = HashHelper::make($i_user['password']);

            $one = $this->userRepo->updateOne($one->id, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            # Delete UserRole
            $this->userRoleRepo->deleteByUserId($one->id);

            // Insert UserRole
            foreach ($i_user_roles as $i_user_role) {
                $i_two = [
                    'user_id'      => $one->id,
                    'role_id'      => $i_user_role,
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];
                $two   = $this->userRoleRepo->createOne($i_two);

                if (!$two) {
                    DB::rollback();
                    return $result;
                }
            }

            # Delete UserPosition
            $this->userPositionRepo->deleteByUserId($one->id);

            // Insert UserPosition
            foreach ($i_user_positions as $i_user_position) {
                $i_three = [
                    'user_id'      => $one->id,
                    'position_id'  => $i_user_position,
                    'created_by'   => $one->created_by,
                    'updated_by'   => 0,
                    'created_date' => $one->created_date,
                    'updated_date' => null,
                    'active'       => true
                ];
                $three   = $this->userPositionRepo->createOne($i_three);

                if (!$three) {
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

            $one = $this->userRepo->deactivateOne($id) ? true : false;

            if (!$one) {
                DB::rollBack();
                return $result;
            }

            # Deactivate UserRole
            $this->userRoleRepo->deactivateByUserId($id);

            # Deactivate UserPosition
            $this->userPositionRepo->deactivateByUserId($id);

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

            $one = $this->userRepo->destroyOne($id) ? true : false;
            if (!$one) {
                DB::rollBack();
                return $result;
            }

            # Delete UserRole
            $this->userRoleRepo->deleteByUserId($id);

            # Delete UserPosition
            $this->userPositionRepo->deleteByUserId($id);

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
        $user_id   = $filter['user_id'];

        $filtered = $this->userRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($user_id != 0)
            $filtered = $filtered->where('id', $user_id);

        return [
            'users' => $filtered->values()
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
    public function changePassword($data)
    {
        if ($data['password'] == $data['new_password'])
            return ['error' => 'Mật khẩu cũ và mới không được trùng nhau.', 'status_code' => 404];

        $user = $this->userRepo->findOneActive($this->user->id);
        if (!$user)
            return ['error' => 'Người dùng không tồn tại.', 'error_en' => 'user is not exist', 'status_code' => 401];

        // Xác thực mật khẩu cũ
        $password_check = HashHelper::check($data['password'], $user->password);
        if (!$password_check) {
            return ['error' => 'Mật khẩu không hợp lệ.', 'error_en' => 'password is not correct', 'status_code' => 401];
        }

        // Update password
        $i_one = [
            'password' => HashHelper::make($data['password']),
            'active'   => true
        ];

        $one = $this->userRepo->updateOne($user, $i_one);

        if (!$one)
            return ['error' => 'Kết nối đến máy chủ thất bại, vui lòng làm mới trình duyệt và thử lại.', 'status_code' => 404];

        return ['status_code' => 200];
    }

}