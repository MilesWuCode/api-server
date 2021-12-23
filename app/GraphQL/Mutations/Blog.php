<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Blog
{
    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function create($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user();

        $blog = $user->blogs()->create($args);

        if (count($args['tag']) > 0) {
            $tags = $args['tag'];
            $blog->syncTagsWithType(is_array($tags) ? $tags : [], 'blog');
        }

        if (count($args['gallery']) > 0) {
            $gallery = $args['gallery'];

            foreach ($gallery as $file) {
                if (Storage::disk('temporary')->exists($file)) {
                    $blog->addMediaFromDisk($file, 'temporary')->toMediaCollection('gallery');
                }
            }
        }

        return $blog;
    }
}
