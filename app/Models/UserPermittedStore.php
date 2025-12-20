<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermittedStore extends Pivot
{
    use HasTeamScope;
    //
}
