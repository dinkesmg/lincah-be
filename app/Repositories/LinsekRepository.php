<?php

namespace App\Repositories;

use App\Models\Linsek;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class LinsekRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Linsek::query();
        if ($request) {
            
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
        $query = Linsek::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'content' => 'required',
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
        return Linsek::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        $linsek = Linsek::first();

        if ($linsek) {
            $linsek->update($data);
            return $linsek;
        }

        return Linsek::create($data);
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

        $path = "{$envName}/Linsek/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

