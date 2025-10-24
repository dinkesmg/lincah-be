<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Forum;
use App\Models\User;
use App\Models\Topik;
use App\Models\Kecamatan;
use App\Repositories\ForumRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    protected $forumRepository;

    public function __construct()
    {
        $this->forumRepository = new ForumRepository();

        $this->middleware('permission:forum-index|forum-create|forum-edit|forum-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:forum-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:forum-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:forum-show', ['only' => ['show']]);
        $this->middleware('permission:forum-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $user = User::get();
        $topik = Topik::get();
        $kecamatan = Kecamatan::get();

        $forums = $this->forumRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.forum.index', compact('forums', 'user', 'topik', 'kecamatan'));
    }

    public function create()
    {
        $topik = Topik::get();
        $kecamatan = Kecamatan::get();

        return view('backend.forum.create', compact('topik', 'kecamatan'));
    }

    public function store(Request $request)
    {
        $validation = $this->forumRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->forumRepository->create($data);
            DB::commit();
            return redirect(route('forum.index'))->with('success', 'Forum Diskusi Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $topik = Topik::get();
        $kecamatan = Kecamatan::get();

        $forum = $this->forumRepository->findByUuid($uuid);
        return view('backend.forum.detail', compact('forum', 'topik', 'kecamatan'));
    }

    public function edit($uuid)
    {
        $topik = Topik::get();
        $kecamatan = Kecamatan::get();

        $forum = $this->forumRepository->findByUuid($uuid);
        return view('backend.forum.create', compact('forum', 'topik', 'kecamatan'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->forumRepository->findByUuid($uuid); 
        $validation = $this->forumRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->forumRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('forum.index'))->with('success', 'Forum Diskusi Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->forumRepository->delete($uuid);
        return back()->with('success', 'Forum Diskusi Berhasil Dihapus');
    }
}
