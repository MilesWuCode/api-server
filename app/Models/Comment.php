<?php

namespace App\Models;

use BeyondCode\Comments\Comment as Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Comment extends Model implements ReactableInterface
{
    use Reactable;
}
