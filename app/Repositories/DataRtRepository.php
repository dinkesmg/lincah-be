<?php

namespace App\Repositories;

use App\Models\DataRt;
use App\Models\JenisKasus;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class DataRtRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = DataRt::query();
        if ($request) {

            if ($request->kecamatan_id) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->kelurahan_id) {
                $query->where('kelurahan_id', $request->kelurahan_id);
            }

            if ($request->rw_id) {
                $query->where('rw_id', $request->rw_id);
            }

            if ($request->rt_id) {
                $query->where('rt_id', $request->rt_id);
            }
            
        }

        return $query;
    }
    public function search(){
        $query = DataRt::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'bulan' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'rw_id' => 'required',
            'rt_id' => 'required',
            'keterpaparan' => 'required',
            'kerentanan' => 'required',
            'potensial_dampak' => 'required',
            'jumlah_kasus' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
        return DataRt::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();
        $data['jenis_kasus_id'] = $jenisKasus->id;
        $data['tahun'] = session('selected_year', date('Y'));

        if (isset($data['image']) && $data['image']->isValid()) {
            if (isset($model) && $model->image) {
                Storage::disk('public')->delete($model->image);
            }
            $uploadedFile_image = $data['image'];
            $fileExtension_image = $uploadedFile_image->getClientOriginalExtension();
            $fileName_image = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_image;
            $directory_image = 'image/' . date('Y/m/d');
            $filePath_image = $uploadedFile_image->storeAs($directory_image, $fileName_image, 'public');
            $data['image'] = $filePath_image;
        }

        return DataRt::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        if (isset($data['image']) && $data['image']->isValid()) {
            if (isset($model) && $model->image) {
                Storage::disk('public')->delete($model->image);
            }
            $uploadedFile_image = $data['image'];
            $fileExtension_image = $uploadedFile_image->getClientOriginalExtension();
            $fileName_image = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_image;
            $directory_image = 'image/' . date('Y/m/d');
            $filePath_image = $uploadedFile_image->storeAs($directory_image, $fileName_image, 'public');
            $data['image'] = $filePath_image;
        }

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

        $path = "{$envName}/DataRt/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

