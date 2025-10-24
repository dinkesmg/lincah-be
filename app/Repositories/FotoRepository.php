<?php

namespace App\Repositories;

use App\Models\Foto;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class FotoRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Foto::query();
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
        $query = Foto::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [];
        
        $fileRule = 'image|max:5120|mimes:jpg,jpeg,png';
        
        $rules['file'] = $isUpdate ? "nullable|$fileRule" : "required|$fileRule";

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
        return Foto::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        
        if (isset($data['file']) && $data['file']->isValid()) {
            if (isset($model) && $model->file) {
                Storage::disk('public')->delete($model->file);
            }
            $uploadedFile_file = $data['file'];
            $fileExtension_file = $uploadedFile_file->getClientOriginalExtension();
            $fileName_file = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_file;
            $directory_file = 'file/' . date('Y/m/d');
            $filePath_file = $uploadedFile_file->storeAs($directory_file, $fileName_file, 'public');
            $data['file'] = $filePath_file;
        }
        return Foto::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($data['file']) && $data['file']->isValid()) {
            if (isset($model) && $model->file) {
                Storage::disk('public')->delete($model->file);
            }
            $uploadedFile_file = $data['file'];
            $fileExtension_file = $uploadedFile_file->getClientOriginalExtension();
            $fileName_file = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_file;
            $directory_file = 'file/' . date('Y/m/d');
            $filePath_file = $uploadedFile_file->storeAs($directory_file, $fileName_file, 'public');
            $data['file'] = $filePath_file;
        }

        return $model->update($data);

    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($model->file)) {
            Storage::disk('public')->delete($model->file);
        }

        $model->delete();
    }

    private function uploadImage($file, $name)
    {
        $envName = env('APP_NAME', 'default');

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $path = "{$envName}/Foto/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

