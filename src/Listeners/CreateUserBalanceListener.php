<?php

namespace Nurdaulet\FluxWallet\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserBalanceListener
{


    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        config('flux-wallet.models.balance')::updateOrCreate([
            'user_id' => $event->user->id
        ],
            [
            'bonus' => 0
        ]);
    }
}
