<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelJutsu\Artifact\Concerns\HasArtifacts;

class Income extends Model
{
    use SoftDeletes, HasArtifacts, HasTeamScope;

    protected $fillable = [
        'team_id',
        'income_type_id',
        'store_id',
        'employee_id',
        'amount',
        'income_date',
        'reference',
        'note',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function incomeType()
    {
        return $this->belongsTo(IncomeType::class, 'income_type_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents()
    {
        return $this->manyArtifacts('documents');
    }
}
