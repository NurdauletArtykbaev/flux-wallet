<?php

namespace Nurdaulet\FluxWallet\Traits;

use Nurdaulet\FluxWallet\Filters\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeApplyFilters(Builder $builder, ModelFilter $modelFilter, array $filters): Builder
    {
        return $modelFilter->apply($builder, $filters);
    }
}
