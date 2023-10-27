
# Пакет sms-kz для Laravel
Пакет flux-wallet - онлайн оплата .

Установите пакет с помощью Composer:

``` bash
 composer require Nurdaulet/flux-wallet
```

## Конфигурация
После установки пакета, вам нужно опубликовать конфигурационный файл. Вы можете сделать это с помощью следующей команды:
``` bash
php artisan vendor:publish --tag=flux-wallet-config
```

## Использование

```php
use Nurdaulet\FluxWallet\Facades\WalletFacade;
WalletFacade::to('phone')->text("message")->send();
```

[//]: # (php artisan vendor:publish --provider="Nurdaulet\FluxItems\FluxItemsServiceProvider")


