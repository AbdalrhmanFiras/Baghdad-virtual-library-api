<?php

namespace App\Http\Controllers;

use App\Helper\FileHelper;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * @tags News Endpoint
 */
class NewsController extends Controller
{
    /**
     * Create News
     *
     * `Admin(only)`
     */
    public function store(StoreNewsRequest $request)
    {
        $data = $request->validated();

        $news = News::create($data);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHelper::ImageUpload($file, 'news', null);
        }
        $news->image()->create([
            'url' => $path,
            'type' => 'news',
        ]);

        return $this->responseSuccess(['data' => new NewsResource($news)], 'News created sucessfully.', 201);

    }

    /**
     * Update News
     *
     * `Admin(only)`
     */
    public function update(UpdateNewsRequest $request, $id)
    {
        $data = $request->validated();
        try {
            $new = News::findOrFail($id);

            if (! $new) {
                return $this->responseError(null, 'News not found.', 404);
            }

            $path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = FileHelper::ImageUpload($file, 'news', null, 's3');
            }

            DB::transaction(function () use ($new, $data, $path) {
                if ($path) {
                    FileHelper::UpdateImage($new, $path, 's3');
                }
                $new->update($data);
            });
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'News not found.', 404);

        }

        return $this->responseSuccess([
            'data' => new NewsResource($new->fresh('image'))],
            'News updated successfully', 200);
    }

    /**
     * Delete News
     *
     * `Admin(only)`
     */
    public function delete($id)
    {
        try {
            $news = News::findOrFail($id);
            DB::transaction(function () use ($news) {
                FileHelper::DeleteImage($news);
                $news->delete();
            });

            return $this->responseSuccess(null, 'News deleted successfully.', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'News not found.', 404);

        }

    }

    /**
     * Show News
     */
    public function show($id)
    {
        try {
            $news = News::findOrFail($id);

            return $this->responseSuccess(['data' => new NewsResource($news)], 'News created sucessfully.', 201);
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'News not found.', 404);
        }
    }

    /**
     * Show(All) News
     */
    public function index()
    {
        $news = News::paginate(10);
        if ($news->isEmpty()) {
            return $this->responseError(null, 'News not found.', 404);
        }

        return $this->responseSuccess([
            'data' => NewsResource::collection($news), 'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ],
        ], 'News fetched successfully.', 200);
    }
}
