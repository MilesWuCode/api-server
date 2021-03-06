<?php

namespace App\Transformers;

use App\Models\Blog;
use League\Fractal\TransformerAbstract;

class BlogTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        'tag', 'gallery',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Blog $blog): array
    {
        return [
            'id' => (int) $blog->id,
            'title' => $blog->title,
            'body' => $blog->body,
            'status' => (boolean) $blog->status,
            'like' => $blog->like,
            'like_count' => (int) $blog->like_count,
            'dislike_count' => (int) $blog->dislike_count,
            'publish_at' => $blog->publish_at?->format('Y-m-d'),
            'created_at' => $blog->created_at->format('Y-m-d H:i:s'),
            'updated_at' => (string) $blog->updated_at,
        ];
    }

    public function includeTag(Blog $blog)
    {
        return $this->collection($blog->tags, new TagTransformer);
    }

    public function includeGallery(Blog $blog)
    {
        return $this->collection($blog->getMedia('gallery'), new MediaTransformer);
    }
}
