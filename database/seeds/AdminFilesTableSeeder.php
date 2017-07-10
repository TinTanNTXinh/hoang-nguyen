<?php

use Illuminate\Database\Seeder;
use App\Repositories\FileRepositoryInterface;

class AdminFilesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(FileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array_name = [
            'admin',
            'superadmin'
        ];

        foreach($array_name as $key => $name){
            \App\File::create([
                'code'         => $this->repository->generateCode('FILE'),
                'name'         => $name,
                'extension'    => 'jpg',
                'mime_type'    => 'image/jpeg',
                'path'         => 'assets/img/a'.$key.'.jpg',
                'size'         => 0,
                'table_name'   => 'users',
                'table_id'     => ++$key,
                'note'         => '',
                'created_date' => date('Y-m-d H:i:s'),
                'updated_date' => null,
                'active'       => true
            ]);
        }
    }
}
