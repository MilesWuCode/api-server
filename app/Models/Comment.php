<?php

namespace App\Models;

use BeyondCode\Comments\Comment as Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property string $comment
 * @property bool $is_approved
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \App\Models\User|null $commentator
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Cog\Laravel\Love\Reactant\Models\Reactant $loveReactant
 * @method static \Illuminate\Database\Eloquent\Builder|Comment approved()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment joinReactionCounterOfType(string $reactionTypeName, ?string $alias = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment joinReactionTotal(?string $alias = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereNotReactedBy(\Cog\Contracts\Love\Reacterable\Models\Reacterable $reacterable, ?string $reactionTypeName = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereReactedBy(\Cog\Contracts\Love\Reacterable\Models\Reacterable $reacterable, ?string $reactionTypeName = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $love_reactant_id
 * @method static \Database\Factories\CommentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLoveReactantId($value)
 */
class Comment extends Model implements ReactableInterface
{
    use Reactable;
    use HasFactory;
}
