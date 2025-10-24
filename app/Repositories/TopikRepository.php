<?php

namespace App\Repositories;

use App\Models\Topik;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class TopikRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Topik::query();
        if ($request) {

            if ($request->nama) {
                $query->where('nama', 'like', "%$request->nama%");
            }
            
        }

        return $query;
    }
    public function search(){
        $query = Topik::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'nama' => 'required',
        ];
        if($isUpdate){
            if ($id) {
                
            }
        }
        $messages = [

        ];
        return ['rules' => $rules, 'messages' => $messages];
    }

    public function findByUuid(string $uuid)
    {
        return Topik::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        
        return Topik::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        

        return $model->update($data);

    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        

        $model->delete();
    }

    private function uploadImage($file, $name)
    {
        $envName = env('APP_NAME', 'default');

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $path = "{$envName}/Topik/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

