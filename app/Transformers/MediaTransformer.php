<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaTransformer extends TransformerAbstract
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
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Media $media): array
    {
        return [
            'id' => (int) $media->id,
            'file_name' => $media->file_name,
            'original_url' => $media->original_url,
            'size' => $media->size,
            'created_at' => $media->created_at->format('Y-m-d H:i:s'),
            'updated_at' => (string) $media->updated_at,
        ];
    }
}
