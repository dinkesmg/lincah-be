<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Topik;
use App\Repositories\TopikRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TopikController extends Controller
{
    protected $topikRepository;

    public function __construct()
    {
        $this->topikRepository = new TopikRepository();

        $this->middleware('permission:topik-index|topik-create|topik-edit|topik-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:topik-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:topik-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:topik-show', ['only' => ['show']]);
        $this->middleware('permission:topik-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $topiks = $this->topikRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.topik.index', compact('topiks'));
    }

    public function create()
    {
        return view('backend.topik.create');
    }

    public function store(Request $request)
    {
        $validation = $this->topikRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->topikRepository->create($data);
            DB::commit();
            return redirect(route('topik.index'))->with('success', 'Topik Forum Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $topik = $this->topikRepository->findByUuid($uuid);
        return view('backend.topik.create', compact('topik'));
    }

    public function edit($uuid)
    {
        $topik = $this->topikRepository->findByUuid($uuid);
        return view('backend.topik.create', compact('topik'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->topikRepository->findByUuid($uuid); 
        $validation = $this->topikRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->topikRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('topik.index'))->with('success', 'Topik Forum Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->topikRepository->delete($uuid);
        return back()->with('success', 'Topik Forum Berhasil Dihapus');
    }
}
