<?php

namespace Nurdaulet\FluxWallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nurdaulet\FluxWallet\Interfaces\IBillable;
use Nurdaulet\FluxWallet\Traits\Billable;


/**
 * @property int $id
 * @property string $contract
 */
class User extends Model implements IBillable
{
    use  HasFactory, Notifiable, Billable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function getBillableId()
    {
        $billableId = time() . rand(10000, 010000) . $this->id;
        if (strlen($billableId) > 15) {
            $billableId = substr($billableId, strlen($billableId) - 15);
        }
        return $billableId;
    }


    protected $casts = [
        'identify_status' => 'integer',
        'is_identified' => 'boolean',
        'is_banned' => 'boolean',
        'delivery_times' => 'json',
        'graphic_works' => 'json',
        'is_enabled_notification' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar_url'];

    public function balance()
    {
        return $this->hasOne(Balance::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
