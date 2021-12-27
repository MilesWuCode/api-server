<?php

namespace App\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

/**
 * App\Models\Blog
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $body
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $publish_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $love_reactant_id
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $gallery
 * @property-read int|null $gallery_count
 * @property-read mixed $dislike_count
 * @property-read mixed $like_count
 * @property-read \Cog\Laravel\Love\Reactant\Models\Reactant|null $loveReactant
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\BlogFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog joinReactionCounterOfType(string $reactionTypeName, ?string $alias = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog joinReactionTotal(?string $alias = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLoveReactantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereNotReactedBy(\Cog\Contracts\Love\Reacterable\Models\Reacterable $reacterable, ?string $reactionTypeName = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog wherePublishAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereReactedBy(\Cog\Contracts\Love\Reacterable\Models\Reacterable $reacterable, ?string $reactionTypeName = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withAnyTagsOfAnyType($tags)
 * @mixin \Eloquent
 */
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

        $this->addMediaCollection('gallery')
        // ->acceptsFile(function (File $file) {
        //     return $file->mimeType === 'image/jpeg';
        // })
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

    public function gallery(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'gallery');
    }

    public function setTag(array $tag = [])
    {
        $this->syncTagsWithType($tag, 'blog');

        return $this;
    }

    public function setFile(string $collection, array $files = [])
    {
        foreach ($files as $file) {
            if (Storage::disk('temporary')->exists($file)) {
                $this->addMediaFromDisk($file, 'temporary')->toMediaCollection($collection);
            }
        }

        return $this;
    }

    public function getLikeCountAttribute()
    {
        // list n+1: ->with(['tags', 'loveReactant.reactionCounters', 'loveReactant.reactionTotal'])
        return $this->viaLoveReactant()
            ->getReactionCounterOfType('Like')
            ->getCount();
    }

    public function getDislikeCountAttribute()
    {
        // list n+1: ->with(['tags', 'loveReactant.reactionCounters', 'loveReactant.reactionTotal'])
        return $this->viaLoveReactant()
            ->getReactionCounterOfType('Dislike')
            ->getCount();
    }
}
