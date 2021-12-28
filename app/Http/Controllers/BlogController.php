<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogFileAddRequest;
use App\Http\Requests\BlogFileDelRequest;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Models\Blog;
use App\Transformers\BlogTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\Fractal\Facades\Fractal;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Blog::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'page' => 'sometimes|numeric|min:1',
            'limit' => 'sometimes|numeric|min:1|max:100',
            'sort' => 'sometimes|in:id_asc,id_desc,updated_at_asc,updated_at_desc',
        ])->validate();

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 5);
        $sort = $request->input('sort', 'id_asc');

        [$column, $order] = preg_split('/_(?=(asc|desc)$)/', $sort);

        $blogs = $request->user()
            ->blogs()
            ->with(['tags', 'loveReactant.reactionCounters'])
            ->orderBy($column, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return Fractal::create($blogs, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tag')
        // ->includeTags()
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BlogStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BlogStoreRequest $request): JsonResponse
    {
        $blog = $request->user()->blogs()->create($request->all());

        if ($request->has('tag')) {
            $blog->setTag($request->input('tag') ?? []);
        }

        $blog->setFile('gallery', $request->input('gallery') ?? []);

        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tag')
        // ->includeTags()
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Blog $blog): JsonResponse
    {
        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tag')
        // ->includeTags()
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BlogUpdateRequest  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BlogUpdateRequest $request, Blog $blog): JsonResponse
    {
        $blog->update($request->all());

        if ($request->has('tag')) {
            $blog->setTag($request->input('tag') ?? []);
        }

        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tag')
        // ->includeTags()
            ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog): Response
    {
        return response($blog->delete(), 200);
    }

    /**
     * File add
     *
     * @param BlogFileAddRequest $request
     * @param Blog $blog
     * @return JsonResponse
     */
    public function fileAdd(BlogFileAddRequest $request, Blog $blog): JsonResponse
    {
        $blog->setFile($request->input('collection'), [$request->input('file')]);

        return Fractal::create($blog, new BlogTransformer())
            ->parseIncludes($request->input('collection'))
            ->respond();
    }

    /**
     * File delete
     *
     * @param BlogFileDelRequest $request
     * @param Blog $blog
     * @return JsonResponse
     */
    public function fileDel(BlogFileDelRequest $request, Blog $blog): JsonResponse
    {
        // 配合 BlogFileDelRequest 檢查 media_id 是否存在於資料表
        // $mediaItems = $blog->getMedia($request->input('collection'));

        // $mediaItem = $mediaItems->find($request->input('media_id'));

        // if($mediaItem){
        //     return response()->json(['message' => 'done']);
        // }else{
        //     return response()->json(['message' => 'media not found.'], 404);
        // }

        // 補捉model錯誤訊息
        try {
            $blog->delFile($request->input('collection'), $request->input('media_id'));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['message' => 'done']);
    }
}
