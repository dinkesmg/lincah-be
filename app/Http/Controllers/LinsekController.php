<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Linsek;
use App\Repositories\LinsekRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LinsekController extends Controller
{
    protected $linsekRepository;

    public function __construct()
    {
        $this->linsekRepository = new LinsekRepository();

        $this->middleware('permission:linsek-index|linsek-create|linsek-edit|linsek-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:linsek-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:linsek-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:linsek-show', ['only' => ['show']]);
        $this->middleware('permission:linsek-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $linseks = Linsek::first();
        $request->flash();
        return view('backend.linsek.index', compact('linseks'));
    }

    public function create()
    {
        $linseks = Linsek::first();
        return view('backend.linsek.create', compact('linseks'));
    }

    public function store(Request $request)
    {
        $validation = $this->linsekRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->linsekRepository->create($data);
            DB::commit();
            return redirect(route('linsek.index'))->with('success', 'Linsek Tematik Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
