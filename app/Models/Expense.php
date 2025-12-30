<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelJutsu\Artifact\Concerns\HasArtifacts;

class Expense extends Model
{
    use SoftDeletes, HasArtifacts, HasTeamScope;

    protected $fillable = [
        'team_id',
        'expense_type_id',
        'store_id',
        'employee_id',
        'amount',
        'expense_date',
        'reference',
        'note',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
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
