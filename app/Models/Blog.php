<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Blog extends Model
{
    use HasTags;
    use HasFactory;

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
}
