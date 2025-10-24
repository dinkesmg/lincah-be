<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class VideoRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Video::query();
        if ($request) {
            
        }

        return $query;
    }
    public function search(){
        $query = Video::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'link' => 'required',
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
        return Video::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        
        if (isset($data['thumbnail']) && $data['thumbnail']->isValid()) {
            if (isset($model) && $model->thumbnail) {
                Storage::disk('public')->delete($model->thumbnail);
            }
            $uploadedFile_thumbnail = $data['thumbnail'];
            $fileExtension_thumbnail = $uploadedFile_thumbnail->getClientOriginalExtension();
            $fileName_thumbnail = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_thumbnail;
            $directory_thumbnail = 'thumbnail/' . date('Y/m/d');
            $filePath_thumbnail = $uploadedFile_thumbnail->storeAs($directory_thumbnail, $fileName_thumbnail, 'public');
            $data['thumbnail'] = $filePath_thumbnail;
        }
        return Video::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($data['thumbnail']) && $data['thumbnail']->isValid()) {
            if (isset($model) && $model->thumbnail) {
                Storage::disk('public')->delete($model->thumbnail);
            }
            $uploadedFile_thumbnail = $data['thumbnail'];
            $fileExtension_thumbnail = $uploadedFile_thumbnail->getClientOriginalExtension();
            $fileName_thumbnail = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_thumbnail;
            $directory_thumbnail = 'thumbnail/' . date('Y/m/d');
            $filePath_thumbnail = $uploadedFile_thumbnail->storeAs($directory_thumbnail, $fileName_thumbnail, 'public');
            $data['thumbnail'] = $filePath_thumbnail;
        }

        return $model->update($data);

    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($model->thumbnail)) {
            Storage::disk('public')->delete($model->thumbnail);
        }

        $model->delete();
    }

    private function uploadImage($file, $name)
    {
        $envName = env('APP_NAME', 'default');

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $path = "{$envName}/Video/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

