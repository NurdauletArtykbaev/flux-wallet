<?php

namespace Nurdaulet\FluxWallet\Filters;

use Carbon\Carbon;

class TransactionFilter extends ModelFilter
{

    public function user_id($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->where('user_id',$value);
    }

    public function from_date($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->whereDate('created_at', '>=', Carbon::create($value)->format('Y.m.d'));
    }

    public function to_date($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->whereDate('created_at', '<=', Carbon::create($value)->format('Y.m.d'));
    }
}
