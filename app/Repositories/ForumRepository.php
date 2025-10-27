<?php

namespace App\Repositories;

use App\Models\Forum;
use Illuminate\Support\Facades\Storage;
use Satriotol\Fastcrud\Traits\RemovesFiles;
use Illuminate\Support\Str;
use DB;

class ForumRepository
{
    use RemovesFiles;

    public function getAll(array $params = [], $request = null)
    {
        $query = Forum::query();
        $user = auth()->user();

        if ($request) {

            if (!$user->hasRole('SUPERADMIN')) {
                $query->where('user_id', $user->id);
            } else {
                if ($request->user_id) {
                    $query->where('user_id', $request->user_id);
                }
            }

            if ($request->topik_id) {
                $query->whereUserId($request->topik_id);
            }

            if ($request->kecamatan_id) {
                $query->whereUserId($request->kecamatan_id);
            }
            
        }

        return $query;
    }
    public function search(){
        $query = Forum::query();
        return $query;
    }
    public function validate($isUpdate = false, $id = null)
    {
        $user = auth()->user();
        $role = $user->getRole()->name ?? null;
     
        $rules = [
            'topik_id' => 'required',
            'judul' => 'required',
            'hasil' => 'required',
            'tanggal' => 'required',
            'rencana_tindak_lanjut' => 'required',
            'link_dokumentasi' => 'nullable',
        ];

        if ($role === 'PUSKESMAS') {
            $rules['kecamatan_id'] = 'nullable';
        } else {
            $rules['kecamatan_id'] = 'required';
        }
        
        $fileRule = 'image|max:5120|mimes:jpg,jpeg,png';
        
        $rules['foto'] = $isUpdate ? "nullable|$fileRule" : "required|$fileRule";

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
        return Forum::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        $user = auth()->user();
        $role = $user->getRole()->name ?? null;
     
        $data['user_id'] = $user->id;
        
        if (isset($data['foto']) && $data['foto']->isValid()) {
            if (isset($model) && $model->foto) {
                Storage::disk('public')->delete($model->foto);
            }
            $uploadedFile_foto = $data['foto'];
            $fileExtension_foto = $uploadedFile_foto->getClientOriginalExtension();
            $fileName_foto = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_foto;
            $directory_foto = 'foto/' . date('Y/m/d');
            $filePath_foto = $uploadedFile_foto->storeAs($directory_foto, $fileName_foto, 'public');
            $data['foto'] = $filePath_foto;
        }

        if ($role === 'PUSKESMAS') {
            $wilayah = DB::table('user_wilayah')
                ->where('users_id', $user->id)
                ->select('kecamatan_id')
                ->first();
            if ($wilayah) {
                $data['kecamatan_id'] = $wilayah->kecamatan_id;
            }
        }

        return Forum::create($data);
    }


    public function update(string $uuid, array $data)
    {
        $model = $this->findByUuid($uuid);

        $user = auth()->user();
        $role = $user->getRole()->name ?? null;
        
        if (isset($data['foto']) && $data['foto']->isValid()) {
            if (isset($model) && $model->foto) {
                Storage::disk('public')->delete($model->foto);
            }
            $uploadedFile_foto = $data['foto'];
            $fileExtension_foto = $uploadedFile_foto->getClientOriginalExtension();
            $fileName_foto = date('mdYHis') . '-' . Str::random(8) . '.' . $fileExtension_foto;
            $directory_foto = 'foto/' . date('Y/m/d');
            $filePath_foto = $uploadedFile_foto->storeAs($directory_foto, $fileName_foto, 'public');
            $data['foto'] = $filePath_foto;
        }

        if ($role === 'PUSKESMAS') {
            $wilayah = DB::table('user_wilayah')
                ->where('users_id', $user->id)
                ->select('kecamatan_id')
                ->first();
            if ($wilayah) {
                $data['kecamatan_id'] = $wilayah->kecamatan_id;
            }
        }

        return $model->update($data);

    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        
        if (isset($model->foto)) {
            Storage::disk('public')->delete($model->foto);
        }

        $model->delete();
    }

    private function uploadImage($file, $name)
    {
        $envName = env('APP_NAME', 'default');

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $path = "{$envName}/Forum/{$year}/{$month}/{$day}/{$name}";

        return Storage::disk('minio')->put($path, $file);
    }
}

