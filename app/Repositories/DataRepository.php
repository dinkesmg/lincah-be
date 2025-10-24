<?php

namespace App\Repositories;

use App\Models\Data;
use App\Models\JenisKasus;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;

class DataRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Data::query();
        if ($request) {

            if ($request->kecamatan_id) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->kelurahan_id) {
                $query->where('kelurahan_id', $request->kelurahan_id);
            }
            
        }

        $tahun = session('tahun', date('Y'));
        $query->whereYear('tanggal', $tahun);

        return $query;
    }
    public function search(){
        $query = Data::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $rules = [
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'bulan' => 'required',
            'keterpaparan' => 'required',
            'kerentanan' => 'required',
            'potensial_dampak' => 'required',
            'jumlah_kasus' => 'required',
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
        return Data::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();
        $data['jenis_kasus_id'] = $jenisKasus->id;
        $data['tahun'] = session('selected_year', date('Y'));

        return Data::updateOrCreate(
            [
                'jenis_kasus_id' => $data['jenis_kasus_id'],
                'bulan'          => $data['bulan'],
                'kecamatan_id'   => $data['kecamatan_id'] ?? null,
                'kelurahan_id'   => $data['kelurahan_id'] ?? null,
                'tahun'          => $data['tahun'],
            ],
            [
                'keterpaparan'      => $data['keterpaparan'] ?? 0,
                'kerentanan'        => $data['kerentanan'] ?? 0,
                'potensial_dampak'  => $data['potensial_dampak'] ?? 0,
                'jumlah_kasus'      => $data['jumlah_kasus'] ?? 0,
            ]
        );
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

        $path = "{$envName}/Data/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

