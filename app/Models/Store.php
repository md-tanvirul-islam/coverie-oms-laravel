<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelJutsu\Artifact\Concerns\HasArtifacts;

class Store extends Model
{
    use SoftDeletes, HasArtifacts;

    protected $fillable = [
        'team_id',
        'name',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];

    public function logo()
    {
        return $this->singleArtifact('logo');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UserPermittedStore::class, 'user_id', 'store_id')->withPivot('full_data');
    }
}
