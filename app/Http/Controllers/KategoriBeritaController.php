<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\KategoriBerita;
use App\Repositories\KategoriBeritaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KategoriBeritaController extends Controller
{
    protected $kategori_beritaRepository;

    public function __construct()
    {
        $this->kategori_beritaRepository = new KategoriBeritaRepository();

        $this->middleware('permission:kategori_berita-index|kategori_berita-create|kategori_berita-edit|kategori_berita-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:kategori_berita-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:kategori_berita-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:kategori_berita-show', ['only' => ['show']]);
        $this->middleware('permission:kategori_berita-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $kategori_beritas = $this->kategori_beritaRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.kategori_berita.index', compact('kategori_beritas'));
    }

    public function create()
    {
        return view('backend.kategori_berita.create');
    }

    public function store(Request $request)
    {
        $validation = $this->kategori_beritaRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kategori_beritaRepository->create($data);
            DB::commit();
            return redirect(route('kategori_berita.index'))->with('success', 'Kategori Berita Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $kategori_berita = $this->kategori_beritaRepository->findByUuid($uuid);
        return view('backend.kategori_berita.create', compact('kategori_berita'));
    }

    public function edit($uuid)
    {
        $kategori_berita = $this->kategori_beritaRepository->findByUuid($uuid);
        return view('backend.kategori_berita.create', compact('kategori_berita'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->kategori_beritaRepository->findByUuid($uuid); 
        $validation = $this->kategori_beritaRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->kategori_beritaRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('kategori_berita.index'))->with('success', 'Kategori Berita Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->kategori_beritaRepository->delete($uuid);
        return back()->with('success', 'Kategori Berita Berhasil Dihapus');
    }
}
