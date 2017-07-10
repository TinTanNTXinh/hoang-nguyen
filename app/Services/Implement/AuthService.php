<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRoleRepositoryInterface;
use App\Repositories\GroupRoleRepositoryInterface;
use App\Repositories\RoleRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use App\Common\Helpers\HashHelper;
use DB;
use League\Flysystem\Exception;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Common\Helpers\HttpStatusCodeHelper;

class AuthService implements AuthServiceInterface
{
    private $table_name, $table_names;

    protected $userRepo, $userRoleRepo, $groupRoleRepo, $roleRepo;

    public function __construct(UserRepositoryInterface $userRepo
        , UserRoleRepositoryInterface $userRoleRepo
        , GroupRoleRepositoryInterface $groupRoleRepo
        , RoleRepositoryInterface $roleRepo)
    {
        $this->userRepo          = $userRepo;
        $this->userRoleRepo  = $userRoleRepo;
        $this->groupRoleRepo = $groupRoleRepo;
        $this->roleRepo      = $roleRepo;

        $this->table_name  = 'user';
        $this->table_names = 'users';
    }

    /* AUTH */
    public function authentication($data)
    {
        if ($data['username'] == null || $data['username'] == '' || $data['password'] == null || $data['password'] == '')
            return ['error' => 'Tên tài khoản hoặc mật khẩu không hợp lệ.', 'error_en' => 'username or password is empty', 'status_code' => 404];

        $user = $this->userRepo->findOneActiveByFieldName('username', $data['username']);

        if (!$user) {
            return ['error' => 'Người dùng không tồn tại.', 'error_en' => 'user is not exist', 'status_code' => 401];
        }
        $password_check = HashHelper::check($data['password'], $user->password);
        if (!$password_check) {
            return ['error' => 'Mật khẩu không hợp lệ.', 'error_en' => 'password is not correct', 'status_code' => 401];
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return ['error' => 'Không thể tạo phiên đăng nhập mới.', 'error_en' => 'could_not_create_token', 'status_code' => 401];
            }
            return ['token' => $token, 'status_code' => 200];
        } catch (JWTException $e) {
            return ['error' => 'Không thể tạo phiên đăng nhập mới.', 'error_en' => 'could_not_create_token', 'status_code' => 500];
        }
    }

    public function authorization()
    {
        $jwt_data = $this->getCurrentUser();
        if (!$jwt_data['status']) {
            return [
                'error'       => $jwt_data['error'],
                'error_en'    => $jwt_data['error_en'],
                'status_code' => $jwt_data['http_status_code']
            ];
        }

        $user_data = $this->getInfoCurrentUser($jwt_data['user']);
        if (!$user_data['status']) {
            return [
                'error'       => $user_data['error'],
                'error_en'    => $user_data['error_en'],
                'status_code' => $user_data['http_status_code']
            ];
        }

        $user = $user_data['user'];

        $user_roles = $this->userRoleRepo->findAllActiveByFieldName('user_id', $user->id)->pluck('role_id')->toArray();
        if (!$user_roles)
            return ['error' => 'user_role is not exist', 'status_code' => 401];

        $roles = $this->roleRepo->findAllActiveByIds($user_roles)->sortBy('index')->values();
        if (!$roles)
            return ['error' => 'role is not exist', 'status_code' => 401];

        $group_roles = $this->groupRoleRepo->findAllActive()->sortBy('index')->values();

        return [
            'user'        => $user,
            'roles'       => $roles,
            'group_roles' => $group_roles,
            'status_code' => 201
        ];
    }

    /* USER HELPER */
    public function getCurrentUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return [
                    'status'           => false,
                    'error'            => 'Người dùng không tồn tại.',
                    'error_en'         => 'user_not_found',
                    'http_status_code' => HttpStatusCodeHelper::$unauthorized
                ];
            }
            return [
                'status'           => true,
                'user'             => $user,
                'http_status_code' => HttpStatusCodeHelper::$ok
            ];
        } catch (TokenExpiredException $e) {
            return [
                'status'           => false,
                'error'            => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
                'error_en'         => 'token_expired',
                'http_status_code' => $e->getStatusCode()
            ];
        } catch (TokenInvalidException $e) {
            return [
                'status'           => false,
                'error'            => 'Đăng nhập thất bại. Vui lòng đăng nhập lại.',
                'error_en'         => 'token_invalid',
                'http_status_code' => $e->getStatusCode()
            ];
        } catch (JWTException $e) {
            return [
                'status'           => false,
                'error'            => 'Đăng nhập thất bại. Vui lòng đăng nhập lại.',
                'error_en'         => 'token_absent',
                'http_status_code' => $e->getStatusCode()
            ];
        }
    }

    public function getInfoCurrentUser($user)
    {
        $user = $this->userRepo->findOneActiveSkeleton($user->id);

        if (!$user)
            return [
                'status'           => false,
                'error'            => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
                'error_en'         => 'user is not exist',
                'http_status_code' => HttpStatusCodeHelper::$unauthorized
            ];
        return [
            'status' => true,
            'user'   => $user
        ];
    }

}