<?php

namespace App\Repositories;

use App\Models\Berita;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class BeritaRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Berita::with(['kategori']);
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
        $query = Berita::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'judul' => 'required',
            'deskripsi' => 'required',
            'kategori_berita_id' => 'required',
            'tanggal' => 'required',
        ];
        
        $fileRule = 'image|max:5120|mimes:jpg,jpeg,png';
        
        $rules['gambar'] = $isUpdate ? "nullable|$fileRule" : "required|$fileRule";

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
        return Berita::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        
        if (isset($data['gambar']) && $data['gambar']->isValid()) {
            if (isset($model) && $model->gambar) {
                Storage::disk('public')->delete($model->gambar);
            }
            $uploadedFile_gambar = $data['gambar'];
            $fileExtension_gambar = $uploadedFile_gambar->getClientOriginalExtension();
            $fileName_gambar = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_gambar;
            $directory_gambar = 'gambar/' . date('Y/m/d');
            $filePath_gambar = $uploadedFile_gambar->storeAs($directory_gambar, $fileName_gambar, 'public');
            $data['gambar'] = $filePath_gambar;
        }
        return Berita::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($data['gambar']) && $data['gambar']->isValid()) {
            if (isset($model) && $model->gambar) {
                Storage::disk('public')->delete($model->gambar);
            }
            $uploadedFile_gambar = $data['gambar'];
            $fileExtension_gambar = $uploadedFile_gambar->getClientOriginalExtension();
            $fileName_gambar = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_gambar;
            $directory_gambar = 'gambar/' . date('Y/m/d');
            $filePath_gambar = $uploadedFile_gambar->storeAs($directory_gambar, $fileName_gambar, 'public');
            $data['gambar'] = $filePath_gambar;
        }

        return $model->update($data);

    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($model->gambar)) {
            Storage::disk('public')->delete($model->gambar);
        }

        $model->delete();
    }

    private function uploadImage($file, $name)
    {
        $envName = env('APP_NAME', 'default');

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $path = "{$envName}/Berita/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

