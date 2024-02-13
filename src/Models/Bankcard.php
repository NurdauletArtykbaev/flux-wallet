<?php

namespace Nurdaulet\FluxWallet\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nurdaulet\FluxWallet\Traits\HasFilters;

class Bankcard extends Model
{
    use HasFactory, HasFilters, SoftDeletes;

    protected $fillable = [
        'number',
        'user_id',
        'month',
        'year',
        'bank',
        'country',
        'card_id',
        'recurring_profile_id',
        'provider',
        'card_owner',
        'card_mask',
        'card_type',
        'has_3ds',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('provider', function (Builder $builder) {
            $builder->where('provider', config('flux-wallet.options.payment_provider'));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
