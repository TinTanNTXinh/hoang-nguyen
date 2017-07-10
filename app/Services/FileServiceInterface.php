<?php

namespace App\Services;


interface FileServiceInterface extends BaseServiceInterface
{
    /**
     * @param $data array
     * @param $files \Symfony\Component\HttpFoundation\File\UploadedFile[]
     * @return array
     */
    public function createMultiple($data, $files);

    /**
     * @param $id integer
     * @return mixed
     */
    public function downloadOne($id);
}