<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Models\Comment;
use App\Transformers\CommentTransformer;
use Illuminate\Http\JsonResponse;
use Spatie\Fractal\Facades\Fractal;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Comment::class);
    }

    /**
     * reply list
     *
     * @param ListRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function index(ListRequest $request, Comment $comment): JsonResponse
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 5);
        $sort = $request->input('sort', 'id_asc');

        [$column, $order] = preg_split('/_(?=(asc|desc)$)/', $sort);

        $replies = $comment->comments()
            ->approved()
            ->orderBy($column, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return Fractal::create($replies, new CommentTransformer())
            ->respond();
    }

    /**
     * comment reply
     *
     * @param CommentStoreRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function store(CommentStoreRequest $request, Comment $comment): JsonResponse
    {
        $reply = $comment->comment($request->input('comment'));

        return Fractal::create($reply, new CommentTransformer())
            ->respond();
    }

    /**
     * display comment or reply
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment): JsonResponse
    {
        return Fractal::create($comment, new CommentTransformer())
            ->respond();
    }

    /**
     * Update comment or reply
     *
     * @param  \App\Http\Requests\CommentUpdateRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
    {
        $comment->update($request->all());

        return Fractal::create($comment, new CommentTransformer())
            ->respond();
    }

    /**
     * Remove comment or reply
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        return response($comment->delete(), 200);
    }
}
