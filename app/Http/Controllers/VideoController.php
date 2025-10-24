<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Video;
use App\Repositories\VideoRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    protected $videoRepository;

    public function __construct()
    {
        $this->videoRepository = new VideoRepository();

        $this->middleware('permission:video-index|video-create|video-edit|video-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:video-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:video-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:video-show', ['only' => ['show']]);
        $this->middleware('permission:video-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $videos = $this->videoRepository->getAll(
            [
            ], $request
        )->latest()->paginate();
        $request->flash();
        return view('backend.video.index', compact('videos'));
    }

    public function create()
    {
        return view('backend.video.create');
    }

    public function store(Request $request)
    {
        $validation = $this->videoRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->videoRepository->create($data);
            DB::commit();
            return redirect(route('video.index'))->with('success', 'Video Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($uuid)
    {
        $video = $this->videoRepository->findByUuid($uuid);
        return view('backend.video.create', compact('video'));
    }

    public function edit($uuid)
    {
        $video = $this->videoRepository->findByUuid($uuid);
        return view('backend.video.create', compact('video'));
    }

    public function update(Request $request, $uuid)
    {
        $model = $this->videoRepository->findByUuid($uuid); 
        $validation = $this->videoRepository->validate(true, $model->id);
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->videoRepository->update($uuid, $data);
            DB::commit();
            return redirect(route('video.index'))->with('success', 'Video Berhasil Terupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $this->videoRepository->delete($uuid);
        return back()->with('success', 'Video Berhasil Dihapus');
    }
}
