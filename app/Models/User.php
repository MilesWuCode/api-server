<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use BeyondCode\Comments\Contracts\Commentator;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $love_reacter_id
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $avatar
 * @property-read int|null $avatar_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Blog[] $blogs
 * @property-read int|null $blogs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read string $avatar_url
 * @property-read \Cog\Laravel\Love\Reacter\Models\Reacter|null $loveReacter
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Todo[] $todos
 * @property-read int|null $todos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User verified()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLoveReacterId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Verify[] $verifies
 * @property-read int|null $verifies_count
 */
class User extends Authenticatable implements MustVerifyEmail, HasMedia, ReacterableInterface, Commentator
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

    public function verifies(): HasMany
    {
        return $this->hasMany(Verify::class);
    }

    public function verifyCode(string $code): bool
    {
        return $this->verifies()
            ->where('code', $code)
            ->where('expires', '>=', now())
            ->count() === 1;
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

    // ??????
    public function avatar(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'avatar');
    }

    // ??????
    public function getAvatarUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    // Like,Dislike,null
    public function setLike($reactant, ?string $reactionType)
    {
        $reacterFacade = $this->viaLoveReacter();

        $types = ['Like', 'Dislike'];

        foreach ($types as $type) {
            if ($type !== $reactionType && $reacterFacade->hasReactedTo($reactant, $type)) {
                $reacterFacade->unreactTo($reactant, $type);
            }
        }

        if ($reactionType && $reacterFacade->hasNotReactedTo($reactant, $reactionType)) {
            $reacterFacade->reactTo($reactant, $reactionType);
        }
    }

    /**
     * Check if a comment for a specific model needs to be approved.
     * @param mixed $model
     * @return bool
     */
    public function needsCommentApproval($model): bool
    {
        return false;
    }
}
