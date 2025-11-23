<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;
use App\Models\RT;
use App\Repositories\RTRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RTController extends Controller
{
    protected $rtRepository;

    public function __construct()
    {
        $this->rtRepository = new RTRepository();

        $this->middleware('permission:rt-index|rt-create|rt-edit|rt-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:rt-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:rt-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:rt-show', ['only' => ['show']]);
        $this->middleware('permission:rt-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $rts = $this->rtRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.rt.index', compact('rts'));
    }

    public function create()
    {
        $kecamatan = Kecamatan::get();

        return view('backend.rt.create', compact('kecamatan'));
    }

    public function store(Request $request)
    {
        $validation = $this->rtRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->rtRepository->create($data);
            DB::commit();
            return redirect(route('rt.index'))->with('success', 'RT Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $rt = $this->rtRepository->findByUuid($uuid);
        return view('backend.rt.create', compact('rt'));
    }

    public function edit($uuid)
    {
        $kecamatan = Kecamatan::get();

        $rt = $this->rtRepository->findByUuid($uuid);
        return view('backend.rt.create', compact('rt', 'kecamatan'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->rtRepository->findByUuid($uuid); 
        $validation = $this->rtRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->rtRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('rt.index'))->with('success', 'RT Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->rtRepository->delete($uuid);
        return back()->with('success', 'RT Berhasil Dihapus');
    }
}
