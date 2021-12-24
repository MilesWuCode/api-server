<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogFileAddRequest;
use App\Http\Requests\BlogFileDelRequest;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
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
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBlogRequest $request): JsonResponse
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
     * @param  \App\Http\Requests\UpdateBlogRequest  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
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
     * @return Response
     */
    public function fileDel(BlogFileDelRequest $request, Blog $blog): Response
    {
        $mediaItems = $blog->getMedia($request->input('collection'));

        $mediaItem = $mediaItems->where('id', $request->input('media_id'))->first();

        return response($mediaItem->delete(), 200);
    }
}
