<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileBlogRequest;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Transformers\BlogTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);
        $sort = $request->get('sort', 'id_asc');

        [$column, $order] = preg_split('/_(?=(asc|desc)$)/', $sort);

        $blogs = $request->user()
            ->blogs()
            ->with('tags')
            ->orderBy($column, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return Fractal::create($blogs, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tags')
        // ->includeTags()
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $blog = $request->user()->blogs()->create($request->all());

        if ($request->has('tags')) {
            $tags = $request->get('tags');

            $blog->syncTagsWithType(is_array($tags) ? $tags : [], 'blog');
        }

        if ($request->has('illustration')) {
            foreach ($request->get('illustration') as $file) {
                if (Storage::disk('temporary')->exists($file)) {
                    $blog->addMediaFromDisk($file, 'temporary')->toMediaCollection('illustration');
                }
            }
        }

        if ($request->has('gallery')) {
            foreach ($request->get('gallery') as $file) {
                if (Storage::disk('temporary')->exists($file)) {
                    $blog->addMediaFromDisk($file, 'temporary')->toMediaCollection('gallery');
                }
            }
        }

        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tags')
        // ->includeTags()
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog): JsonResponse
    {
        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tags')
        // ->includeTags()
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogRequest  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        $blog->update($request->all());

        if ($request->has('tags')) {
            $tags = $request->get('tags');

            $blog->syncTagsWithType(is_array($tags) ? $tags : [], 'blog');
        }

        return Fractal::create($blog, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tags')
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
     * Post File
     *
     * @param  \App\Http\Requests\FileBlogRequest  $request
     */
    public function file(FileBlogRequest $request)
    {
        $fileName = basename($request->file('file')->store('temporary'));

        return response()->json(['file' => $fileName], 200);
    }
}
