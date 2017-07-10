<?php

namespace App\Services\Eloquent;

use App\Services\UserServiceInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\PositionRepositoryInterface;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\GroupRoleRepositoryInterface;
use App\Repositories\UserRoleRepositoryInterface;
use App\Repositories\UserPositionRepositoryInterface;
use App\Repositories\FieldRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;
use Hash;

class UserService implements UserServiceInterface
{
    private $user;
    private $table_name;
    private $fake_pwd;

    protected $userRepo, $positionRepo, $roleRepo, $groupRoleRepo
    , $userRoleRepo, $userPositionRepo, $fieldRepo;

    public function __construct(UserRepositoryInterface $userRepo
        , PositionRepositoryInterface $positionRepo
        , RoleRepositoryInterface $roleRepo
        , GroupRoleRepositoryInterface $groupRoleRepo
        , UserRoleRepositoryInterface $userRoleRepo
        , UserPositionRepositoryInterface $userPositionRepo
        , FieldRepositoryInterface $fieldRepo)
    {
        $this->userRepo         = $userRepo;
        $this->positionRepo     = $positionRepo;
        $this->roleRepo         = $roleRepo;
        $this->groupRoleRepo    = $groupRoleRepo;
        $this->userRoleRepo     = $userRoleRepo;
        $this->userPositionRepo = $userPositionRepo;
        $this->fieldRepo        = $fieldRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'user';

        $this->fake_pwd = substr(config('app.key'), 10);
    }

    public function readAll()
    {
        $all = $this->userRepo->allSkeleton()->get();

        $positions   = $this->positionRepo->allActive();
        $roles       = $this->roleRepo->allActive();
        $group_roles = $this->groupRoleRepo->allActive();

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
        $one = $this->userRepo->oneSkeleton($id)->first();

        $user_roles     = $this->userRoleRepo->readByUserId($one->id)->pluck('role_id')->toArray();
        $user_positions = $this->userPositionRepo->readByUserId($one->id)->pluck('position_id')->toArray();

        return [
            $this->table_name => $one,
            'user_roles'      => $user_roles,
            'user_positions'  => $user_positions
        ];
    }

    public function createOne($data)
    {
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
                'birthday'     => DateTimeHelper::toStringDateTimeClientForDB($i_user['birthday'], 'd/m/Y'),
                'sex'          => $i_user['sex'],
                'email'        => $i_user['email'],
                'note'         => $i_user['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d H:i:s'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->userRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
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
                $two   = $this->userRoleRepo->create($i_two);

                if (!$two) {
                    DB::rollback();
                    return false;
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
                $three   = $this->userPositionRepo->create($i_three);

                if (!$three) {
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
        $i_user           = $data['user'];
        $i_user_roles     = $data['user_roles'];
        $i_user_positions = $data['user_positions'];
//        $i_field          = $data['field'];

        try {
            DB::beginTransaction();

            $one = $this->userRepo->find($i_user['id']);

            $i_one = [
                'fullname'     => $i_user['fullname'],
                'username'     => $i_user['username'],
                'address'      => $i_user['address'],
                'phone'        => $i_user['phone'],
                'birthday'     => DateTimeHelper::toStringDateTimeClientForDB($i_user['birthday'], 'd/m/Y'),
                'sex'          => $i_user['sex'],
                'email'        => $i_user['email'],
                'note'         => $i_user['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d H:i:s'),
                'active'       => true
            ];

            if ($i_user['password'] != $this->fake_pwd)
                $i_one['password'] = Hash::make($i_user['password']);

            $one = $this->userRepo->update($one, $i_one);

            if (!$one) {
                DB::rollback();
                return false;
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
                $two   = $this->userRoleRepo->create($i_two);

                if (!$two) {
                    DB::rollback();
                    return false;
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
                $three   = $this->userPositionRepo->create($i_three);

                if (!$three) {
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

    public function deactivateOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->userRepo->deactivate($id) ? true : false;

            if (!$one) {
                DB::rollBack();
                return false;
            }

            # Deactivate UserRole
            $this->userRoleRepo->deactivateByUserId($id);

            # Deactivate UserPosition
            $this->userPositionRepo->deactivateByUserId($id);

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

            $one = $this->userRepo->destroy($id) ? true : false;
            if (!$one) {
                DB::rollBack();
                return false;
            }

            # Delete UserRole
            $this->userRoleRepo->deleteByUserId($id);

            # Delete UserPosition
            $this->userPositionRepo->deleteByUserId($id);

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

        $filtered = $this->userRepo->allSkeleton();

        $filtered = $this->userRepo->filterFromDateToDate($filtered, 'users.created_at', $from_date, $to_date);

        $filtered = $this->userRepo->filterRangeDate($filtered, 'users.created_at', $range);

        return [
            'users' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

    public function changePassword($data)
    {
        if ($data['password'] == $data['new_password'])
            return ['error' => 'Mật khẩu cũ và mới không được trùng nhau.', 'status_code' => 404];

        $user = $this->userRepo->find($this->user->id);
        if (!$user)
            return ['error' => 'Người dùng không tồn tại.', 'error_en' => 'user is not exist', 'status_code' => 401];

        // Xác thực mật khẩu cũ
        $password_check = Hash::check($data['password'], $user->password);
        if (!$password_check) {
            return ['error' => 'Mật khẩu không hợp lệ.', 'error_en' => 'password is not correct', 'status_code' => 401];
        }

        // Update password
        $i_one = [
            'password' => Hash::make($data['password']),
            'active'   => true
        ];

        $one = $this->userRepo->update($user, $i_one);

        if (!$one)
            return ['error' => 'Kết nối đến máy chủ thất bại, vui lòng làm mới trình duyệt và thử lại.', 'status_code' => 404];

        return ['status_code' => 200];
    }

}