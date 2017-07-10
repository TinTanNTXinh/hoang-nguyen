<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\FileServiceInterface;
use App\Repositories\FileRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class FileService implements FileServiceInterface
{
    private $user;
    private $table_name, $table_names;

    protected $authService, $fileRepo;

    public function __construct(AuthServiceInterface $authService
        , FileRepositoryInterface $fileRepo)
    {
        $this->authService = $authService;
        $this->fileRepo    = $fileRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name  = 'file';
        $this->table_names = 'files';
    }

    public function readAll()
    {
        $all = $this->fileRepo->findAllActiveSkeleton();

        return [
            $this->table_names => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->fileRepo->findOneActiveSkeleton($id);

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

            ];

            $one = $this->fileRepo->createOne($i_one);

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

            // Validate
            $validate_data = $this->validateUpdateOne($data['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->fileRepo->findOneActive($data['id']);

            $i_one = [

                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->fileRepo->updateOne($one->id, $i_one);

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

            // Validate
            $validate_data = $this->validateDeactivateOne($id);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->fileRepo->deactivateOne($id);

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

            $one = $this->fileRepo->destroyOne($id);

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

        $filtered = $this->fileRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        return [
            $this->table_names => $filtered->values()
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

        $one = $this->fileRepo->findOneActive($id);
        if ($one->invoice_id != 0) {
            array_push($msg_error, 'Không thể sửa hay xóa chi phí nhớt đã xuất phiếu thanh toán.');
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

    /* ===== MY FUNCTION ===== */
    public function createMultiple($data, $files)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            foreach ($files as $file) {
                $i_one = [
                    'code'         => null,
                    'name'         => $file->getClientOriginalName(),
                    'extension'    => $file->getClientOriginalExtension(),
                    'mime_type'    => $file->getClientMimeType(),
                    'size'         => $file->getClientSize(),
                    'path'         => '',
                    'table_name'   => $data['table_name'],
                    'table_id'     => $data['table_id'],
                    'note'         => '',
                    'created_by'   => $this->user->id,
                    'updated_by'   => 0,
                    'created_date' => date('Y-m-d'),
                    'updated_date' => null,
                    'active'       => true
                ];

                $one = $this->fileRepo->createOne($i_one);

                if (!$one) {
                    DB::rollback();
                    return $result;
                }

                $one = $this->fileRepo->updateOne($one->id, [
                    'path' => "files/" . $one->table_name . "/" . $one->table_name . "_" . $one->id . "." . $one->extension
                ]);

                // Move file
                $file->move('../public/files/' . $one->table_name, $one->path);
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function downloadOne($id)
    {
        $file         = $this->fileRepo->findOneActive($id);
        $path_to_file = public_path() . "/" . $file->path;
        $headers      = array(
            'Content-Type: ' . $file->mime_type,
            'Content-Disposition: attachment; filename="' . $file->name . '"',
            'Content-Transfer-Encoding: binary',
            'Accept-Ranges: bytes',
            'Content-Length: ' . $file->size
        );
        $name         = $file->name;

        return [
            'path_to_file' => $path_to_file,
            'name'         => $name,
            'headers'      => $headers
        ];
    }

}