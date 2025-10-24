<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Berita;
use App\Models\KategoriBerita;
use App\Repositories\BeritaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
    protected $beritaRepository;

    public function __construct()
    {
        $this->beritaRepository = new BeritaRepository();

        $this->middleware('permission:berita-index|berita-create|berita-edit|berita-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:berita-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:berita-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:berita-show', ['only' => ['show']]);
        $this->middleware('permission:berita-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $beritas = $this->beritaRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.berita.index', compact('beritas'));
    }

    public function create()
    {
        $kategori = KategoriBerita::get();

        return view('backend.berita.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validation = $this->beritaRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->beritaRepository->create($data);
            DB::commit();
            return redirect(route('berita.index'))->with('success', 'Berita Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $berita = $this->beritaRepository->findByUuid($uuid);
        return view('backend.berita.create', compact('berita'));
    }

    public function edit($uuid)
    {
        $berita = $this->beritaRepository->findByUuid($uuid);
        $kategori = KategoriBerita::get();
        return view('backend.berita.create', compact('berita', 'kategori'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->beritaRepository->findByUuid($uuid); 
        $validation = $this->beritaRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->beritaRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('berita.index'))->with('success', 'Berita Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->beritaRepository->delete($uuid);
        return back()->with('success', 'Berita Berhasil Dihapus');
    }
}
