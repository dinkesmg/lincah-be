<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\JenisKasus;
use App\Repositories\JenisKasusRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JenisKasusController extends Controller
{
    protected $jenis_kasusRepository;

    public function __construct()
    {
        $this->jenis_kasusRepository = new JenisKasusRepository();

        $this->middleware('permission:jenis_kasus-index|jenis_kasus-create|jenis_kasus-edit|jenis_kasus-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:jenis_kasus-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:jenis_kasus-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:jenis_kasus-show', ['only' => ['show']]);
        $this->middleware('permission:jenis_kasus-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $jenis_kasuses = $this->jenis_kasusRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.jenis_kasus.index', compact('jenis_kasuses'));
    }

    public function create()
    {
        return view('backend.jenis_kasus.create');
    }

    public function store(Request $request)
    {
        $validation = $this->jenis_kasusRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->jenis_kasusRepository->create($data);
            DB::commit();
            return redirect(route('jenis_kasus.index'))->with('success', 'Jenis Kasus Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $jenis_kasus = $this->jenis_kasusRepository->findByUuid($uuid);
        return view('backend.jenis_kasus.create', compact('jenis_kasus'));
    }

    public function edit($uuid)
    {
        $jenis_kasus = $this->jenis_kasusRepository->findByUuid($uuid);
        return view('backend.jenis_kasus.create', compact('jenis_kasus'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->jenis_kasusRepository->findByUuid($uuid); 
        $validation = $this->jenis_kasusRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->jenis_kasusRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('jenis_kasus.index'))->with('success', 'Jenis Kasus Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->jenis_kasusRepository->delete($uuid);
        return back()->with('success', 'Jenis Kasus Berhasil Dihapus');
    }
}
