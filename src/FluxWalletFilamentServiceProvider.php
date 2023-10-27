<?php

namespace Nurdaulet\FluxWallet;


use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FluxWalletFilamentServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
    ];

    public function configurePackage(Package $package): void
    {
        $this->packageConfiguring($package);
        $package->name('orders-package');
    }
}
