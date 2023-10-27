<?php

namespace Nurdaulet\FluxWallet\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class ModelFilter
{
    protected Builder $builder;

    public function apply(Builder $builder, array $filters)
    {
        $this->builder = $builder;

        foreach ($filters as $column => $value) {
            if (method_exists($this, $column)) {
                call_user_func([$this, $column], $value);
            }
        }

        return $builder;
    }
}
