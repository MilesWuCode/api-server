<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphqlException;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
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

        $blog->setTag($args['tag'] ?? []);

        $blog->setFile('gallery', $args['gallery'] ?? []);

        return $blog;
    }

    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function update($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user();

        // Illuminate\Support\Facades\DB;
        // DB::enableQueryLog();
        // DB::getQueryLog();
        // DB::disableQueryLog();
        // DB::flushQueryLog();

        $blog = $user->blogs()->find($args['id']);

        $blog->update($args);

        $blog->setTag($args['tag'] ?? []);

        return $blog;
    }

    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function addFile($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user();

        $blog = $user->blogs()->find($args['id']);

        $blog->setFile($args['collection'], [$args['file']]);

        return $blog;
    }

    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function delFile($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user();

        $blog = $user->blogs()->find($args['id']);

        try {
            $blog->delFile($args['collection'], $args['media_id']);
        } catch (\Exception $e) {
            throw new GraphqlException(
                $e->getMessage(),
                $e->getFile()
            );
        }

        return $blog;
    }
}
