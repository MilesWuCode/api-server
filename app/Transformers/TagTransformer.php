<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Tags\Tag;

class TagTransformer extends TransformerAbstract
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
    public function transform(Tag $tag)
    {
        return [
            'id' => (int) $tag->id,
            'name' => $tag->name,
            'type' => $tag->type,
        ];
    }
}
