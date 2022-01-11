<?php

namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Comment $comment): array
    {
        return [
            'id' => (int) $comment->id,

            'commentable_type' => $comment->commentable_type,
            'commentable_id' => (int) $comment->commentable_id,

            'comment' => $comment->comment,

            'is_approved' => (boolean) $comment->is_approved,

            'like' => $comment->like,
            'like_count' => (int) $comment->like_count,
            'dislike_count' => (int) $comment->dislike_count,

            'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            'updated_at' => (string) $comment->updated_at,
        ];
    }
}
