<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermittedStore extends Pivot
{
    use HasTeamScope, SoftDeletes;
    //
}
