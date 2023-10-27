<?php

namespace Nurdaulet\FluxWallet\Models;

use  Nurdaulet\FluxWallet\Interfaces\IBillable;
use  Nurdaulet\FluxWallet\Traits\Billable;
use  Nurdaulet\FluxWallet\Traits\HasFilters;
use Faker\Calculator\Iban;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model implements IBillable
{
    use HasFactory, SoftDeletes, HasFilters, Billable;

    protected $fillable = [
        'type',
        'user_id',
        'amount',
        'is_money',
        'is_replenish',
        'money',
        'bonus',
        'transaction_id',
        'status',
        'fields_json'
    ];

    protected $casts = [
        'fields_json' => 'json',
        'is_money' => 'boolean',
        'is_replenish' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
