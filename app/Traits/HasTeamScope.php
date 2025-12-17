<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTeamScope
{
    /**
     * Boot the trait and add the global scope.
     */
    protected static function bootHasTeamScope(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {

            // Resolve team_id safely
            $teamId = self::resolveTeamId();

            if ($teamId) {
                $builder->where(
                    $builder->getModel()->getTable() . '.team_id',
                    $teamId
                );
            }
        });
    }

    /**
     * Resolve current team id
     */
    protected static function resolveTeamId(): ?int
    {
        if (app()->bound('session') && session()->has('team_id')) {
            return session('team_id');
        }

        if (function_exists('getPermissionsTeamId')) {
            return getPermissionsTeamId();
        }

        return null;
    }

    /**
     * Disable team scope for the query
     */
    public function scopeWithoutTeam(Builder $query): Builder
    {
        return $query->withoutGlobalScope('team');
    }
}
