<?php

namespace Nurdaulet\FluxWallet\Filters;

use Carbon\Carbon;

class BankcardFilter extends ModelFilter
{

    public function user_id($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->where('user_id',$value);
    }

    public function id($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->where('id',$value);
    }
}
