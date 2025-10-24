<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Foto;
use App\Repositories\FotoRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FotoController extends Controller
{
    protected $fotoRepository;

    public function __construct()
    {
        $this->fotoRepository = new FotoRepository();

        $this->middleware('permission:foto-index|foto-create|foto-edit|foto-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:foto-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:foto-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:foto-show', ['only' => ['show']]);
        $this->middleware('permission:foto-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $fotos = $this->fotoRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.foto.index', compact('fotos'));
    }

    public function create()
    {
        return view('backend.foto.create');
    }

    public function store(Request $request)
    {
        $validation = $this->fotoRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->fotoRepository->create($data);
            DB::commit();
            return redirect(route('foto.index'))->with('success', 'Foto Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $foto = $this->fotoRepository->findByUuid($uuid);
        return view('backend.foto.create', compact('foto'));
    }

    public function edit($uuid)
    {
        $foto = $this->fotoRepository->findByUuid($uuid);
        return view('backend.foto.create', compact('foto'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->fotoRepository->findByUuid($uuid); 
        $validation = $this->fotoRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->fotoRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('foto.index'))->with('success', 'Foto Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->fotoRepository->delete($uuid);
        return back()->with('success', 'Foto Berhasil Dihapus');
    }
}
