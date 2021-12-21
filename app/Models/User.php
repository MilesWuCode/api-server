<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Laravel\Passport\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;

class User extends Authenticatable implements MustVerifyEmail, HasMedia, ReacterableInterface
{
    use HasApiTokens, HasFactory, Notifiable;
    use InteractsWithMedia;
    use Reacterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // vendor/laravel/passport/src/Bridge/UserRepository.php
    public function findForPassport($username)
    {
        return $this->where('email', $username)
            ->whereNotNull('email_verified_at')
            ->first();
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg'])
            ->useFallbackUrl('http://anonymous-user.jpg')
        // ->useFallbackPath(public_path('anonymous-user.jpg'))
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb');
            });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->performOnCollections('avatar');
    }

    // 照片
    public function avatar(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'avatar');
    }

    // 試作
    public function getAvatarUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    // Toggle Like,Dislike
    public function toggleLove($reactant, $reactionType)
    {
        $reacterFacade = $this->viaLoveReacter();

        $reverse = $reactionType === 'Like' ? 'Dislike' : 'Like';

        if ($reacterFacade->hasReactedTo($reactant, $reverse)) {
            $reacterFacade->unreactTo($reactant, $reverse);
        }

        if ($reacterFacade->hasReactedTo($reactant, $reactionType)) {
            $reacterFacade->unreactTo($reactant, $reactionType);
        } else {
            $reacterFacade->reactTo($reactant, $reactionType);
        }
    }
}
