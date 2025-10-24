<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;
use App\Repositories\KecamatanRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KecamatanController extends Controller
{
    protected $kecamatanRepository;

    public function __construct()
    {
        $this->kecamatanRepository = new KecamatanRepository();

        $this->middleware('permission:kecamatan-index|kecamatan-create|kecamatan-edit|kecamatan-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:kecamatan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:kecamatan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:kecamatan-show', ['only' => ['show']]);
        $this->middleware('permission:kecamatan-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $kecamatans = $this->kecamatanRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.kecamatan.index', compact('kecamatans'));
    }

    public function create(Request $request)
    {
        $request->flash();
        return view('backend.kecamatan.create');
    }

    public function store(Request $request)
    {
        $validation = $this->kecamatanRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kecamatanRepository->create($data);
            DB::commit();
            return redirect(route('kecamatan.index'))->with('success', 'Kecamatan Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $kecamatan = $this->kecamatanRepository->findByUuid($uuid);
        $request->flash();
        return view('backend.kecamatan.create', compact('kecamatan'));
    }

    public function edit(Request $request, $uuid)
    {
        $kecamatan = $this->kecamatanRepository->findByUuid($uuid);
        $request->flash();
        return view('backend.kecamatan.create', compact('kecamatan'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->kecamatanRepository->findByUuid($uuid); 
        $validation = $this->kecamatanRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kecamatanRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('kecamatan.index'))->with('success', 'Kecamatan Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->kecamatanRepository->delete($uuid);
        return back()->with('success', 'Kecamatan Berhasil Dihapus');
    }
}
