<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogFileAddRequest;
use App\Http\Requests\BlogFileDelRequest;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\LikeRequest;
use App\Http\Requests\ListRequest;
use App\Models\Blog;
use App\Transformers\BlogTransformer;
use App\Transformers\CommentTransformer;
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
     * 列表
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
            ->with(['tags', 'loveReactant.reactions', 'loveReactant.reactionCounters'])
            ->orderBy($column, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return Fractal::create($blogs, new BlogTransformer())
        // 手動includes
        // ->parseIncludes('tag')
        // ->includeTags()
            ->respond();
    }

    /**
     * 新增
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
     * 單筆顯示
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
     * 更新
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
     * 刪除
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog): Response
    {
        return response($blog->delete(), 200);
    }

    /**
     * 檔案新增
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
     * 檔案刪除
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

    /**
     * 評論列表
     *
     * @param ListRequest $request
     * @param Blog $blog
     * @return JsonResponse
     */
    public function comments(ListRequest $request, Blog $blog): JsonResponse
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 5);
        $sort = $request->input('sort', 'id_asc');

        [$column, $order] = preg_split('/_(?=(asc|desc)$)/', $sort);

        $comments = $blog->comments()
            ->approved()
            ->orderBy($column, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return Fractal::create($comments, new CommentTransformer())
            ->respond();
    }

    /**
     * 評論新增
     *
     * @param CommentStoreRequest $request
     * @param Blog $blog
     * @return JsonResponse
     */
    public function commentCreate(CommentStoreRequest $request, Blog $blog): JsonResponse
    {
        $comment = $blog->comment($request->input('comment'));

        return Fractal::create($comment, new CommentTransformer())
            ->respond();
    }

    /**
     * 設定喜歡或不喜歡
     *
     * @param LikeRequest $request
     * @param Blog $blog
     * @return void
     */
    public function like(LikeRequest $request, int $id): JsonResponse
    {
        // ? like_count,dislike_count數字不同步問題
        // TODO:修改.env的QUEUE_CONNECTION=sync才會同步
        // TODO:思考該不該顯示數字

        $blog = Blog::with([
            'loveReactant.reactions',
        ])->find($id);

        $request->user()
            ->setLike($blog, $request->input('type', ''));

        return Fractal::create($blog->fresh(), new BlogTransformer())
            ->respond();
    }
}
