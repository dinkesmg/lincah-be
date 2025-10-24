<?php

namespace App\Repositories;

use App\Models\Kelurahan;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class KelurahanRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Kelurahan::query();
        if ($request) {

            if ($request->kecamatan_id) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->nama) {
                $query->where('nama', 'like', "%$request->nama%");
            }
            
        }

       if (!empty($params['filters'])) {
            foreach ($params['filters'] as $key => $value) {
                if ($value) {
                    if (str_contains($key, '.')) {
                        [$relation, $column] = explode('.', $key);
                        $query->whereHas($relation, function ($q) use ($column, $value) {
                            $q->where($column, 'like', "%$value%");
                        });
                    } else {
                        $query->where($key, 'like', "%$value%");
                    }
                }
            }
        }

        if (!empty($params['search']) && !empty($params['search_column'])) {
            $query->where($params['search_column'], 'LIKE', '%' . $params['search'] . '%');
        }

        if (!empty($params['orderBy']) && !empty($params['orderDirection'])) {
            $query->orderBy($params['orderBy'], $params['orderDirection']);
        }

        if (!empty($params['with'])) {
            $query->with($params['with']);
        }

        if (!empty($params['scope'])) {
            foreach ($params['scope'] as $scope) {
                $query->$scope();
            }
        }

        return $query;
    }
    public function search(){
        $query = Kelurahan::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'kecamatan_id' => 'required',
'nama' => 'required',
'koordinat' => 'required',
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
        return Kelurahan::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        
        return Kelurahan::create($data);
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

        $path = "{$envName}/Kelurahan/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

