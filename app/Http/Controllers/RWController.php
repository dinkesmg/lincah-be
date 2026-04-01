<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;
use App\Models\RW;
use App\Repositories\RWRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RWController extends Controller
{
    protected $rwRepository;

    public function __construct()
    {
        $this->rwRepository = new RWRepository();

        $this->middleware('permission:rw-index|rw-create|rw-edit|rw-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:rw-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:rw-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:rw-show', ['only' => ['show']]);
        $this->middleware('permission:rw-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $rws = $this->rwRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.rw.index', compact('rws'));
    }

    public function create()
    {
        $kecamatan = Kecamatan::get();

        return view('backend.rw.create', compact('kecamatan'));
    }

    public function store(Request $request)
    {
        $validation = $this->rwRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->rwRepository->create($data);
            DB::commit();
            return redirect(route('rw.index'))->with('success', 'RW Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $rw = $this->rwRepository->findByUuid($uuid);
        return view('backend.rw.create', compact('rw'));
    }

    public function edit($uuid)
    {
        $kecamatan = Kecamatan::get();
        
        $rw = $this->rwRepository->findByUuid($uuid);
        return view('backend.rw.create', compact('rw', 'kecamatan'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->rwRepository->findByUuid($uuid); 
        $validation = $this->rwRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->rwRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('rw.index'))->with('success', 'RW Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->rwRepository->delete($uuid);
        return back()->with('success', 'RW Berhasil Dihapus');
    }
}
