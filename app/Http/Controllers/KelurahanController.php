<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Repositories\KelurahanRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KelurahanController extends Controller
{
    protected $kelurahanRepository;

    public function __construct()
    {
        $this->kelurahanRepository = new KelurahanRepository();

        $this->middleware('permission:kelurahan-index|kelurahan-create|kelurahan-edit|kelurahan-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:kelurahan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:kelurahan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:kelurahan-show', ['only' => ['show']]);
        $this->middleware('permission:kelurahan-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $kecamatan = Kecamatan::get();
        $kelurahans = $this->kelurahanRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.kelurahan.index', compact('kelurahans', 'kecamatan'));
    }

    public function create(Request $request)
    {
        $kecamatan = Kecamatan::get();

        return view('backend.kelurahan.create', compact('kecamatan'));
    }

    public function store(Request $request)
    {
        $validation = $this->kelurahanRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kelurahanRepository->create($data);
            DB::commit();
            return redirect(route('kelurahan.index'))->with('success', 'Kelurahan Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Request $request, $uuid)
    {
        $kecamatan = Kecamatan::get();

        $kelurahan = $this->kelurahanRepository->findByUuid($uuid);
        $request->flash();
        return view('backend.kelurahan.create', compact('kelurahan', 'kecamatan'));
    }

    public function edit(Request $request, $uuid)
    {
        $kecamatan = Kecamatan::get();

        $kelurahan = $this->kelurahanRepository->findByUuid($uuid);
        $request->flash();
        return view('backend.kelurahan.create', compact('kelurahan', 'kecamatan'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->kelurahanRepository->findByUuid($uuid); 
        $validation = $this->kelurahanRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kelurahanRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('kelurahan.index'))->with('success', 'Kelurahan Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->kelurahanRepository->delete($uuid);
        return back()->with('success', 'Kelurahan Berhasil Dihapus');
    }
}
