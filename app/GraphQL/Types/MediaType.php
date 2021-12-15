<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaType
{
    public function getUrl(Media $media, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): string
    {
        return $media->getUrl();
    }
}
