<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Blog;

class LoveType
{
    public function getLikeCount(Blog $blog, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): string
    {
        return $blog->like_count;
    }

    public function getDislikeCount(Blog $blog, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): string
    {
        return $blog->dislike_count;
    }
}
