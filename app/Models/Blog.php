<?php

namespace App\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class Blog extends Model implements HasMedia, ReactableInterface
{
    use HasTags;
    use HasFactory;
    use InteractsWithMedia;
    use Reactable;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'publish_at',
    ];

    protected $attributes = [
        'status' => true,
        'publish_at' => null,
    ];

    protected $casts = [
        'status' => 'boolean',
        'publish_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        /**
         * ->useDisk('s3')
         * ->singleFile()
         * ->onlyKeepLatest(3)
         * ->withResponsiveImages()
         */

        $this->addMediaCollection('illustration')
            ->acceptsFile(function (File $file) {
                return $file->mimeType === 'image/jpeg';
            });

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg'])
            ->registerMediaConversions(function (Media $media) {
                /**
             * ->border(10, 'black', Manipulations::BORDER_OVERLAY)
             * ->crop('crop-center', 400, 400)
             * ->greyscale()
             * ->quality(80)
             * ->sharpen(10)
             */

                $this
                    ->addMediaConversion('thumb')
                    ->width(320)
                    ->height(240);
            });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(320)
            ->height(240)
            ->performOnCollections('gallery');
    }

    public function illustration(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'illustration');
    }

    public function gallery(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'gallery');
    }

    public function getLikeAttribute()
    {
        return $this->viaLoveReactant()
            ->getReactionCounterOfType('Like')
            ->getCount();
    }

    public function getDislikeAttribute()
    {
        return $this->viaLoveReactant()
            ->getReactionCounterOfType('Dislike')
            ->getCount();
    }
}
