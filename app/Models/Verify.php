<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * App\Models\Verify
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Verify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verify query()
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $expires
 * @method static \Illuminate\Database\Eloquent\Builder|Verify whereExpires($value)
 */
class Verify extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'expires',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::retrieved(function () {
            Verify::where('expires', '<=', now())->delete();
        });

        static::creating(function ($verify) {
            $verify->code = rand(111111, 999999);
            $verify->expires = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
