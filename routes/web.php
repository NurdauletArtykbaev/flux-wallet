<?php


use Nurdaulet\FluxWallet\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 [previous exception] [object] (Illuminate\\Database\\QueryException(code: 42S22): SQLSTATE[42S22]:
Column not found: 1054 Unknown column 'CompanyNameWithPhone' in 'where clause'
(SQL: select count(*) as aggregate from `orders` where (date(`created_at`) <= 2023-06-01 17:17:48) and
(exists (select * from `ads` where `orders`.`ad_id` = `ads`.`id` and exists
    (select * from `users` where `ads`.`user_id` = `users`.`id` and `CompanyNameWithPhone`
            like %551% and `users`.`deleted_at` is null)) or exists
        (select * from `users` where `orders`.`user_id` = `users`.`id` and `FullNameWithPhone` like %551% and `users`.`deleted_at` is null)
    or exists
    (select * from `ads` where `orders`.`ad_id` = `ads`.`id` and `name` like %551%) or
        `address` like %551%) and `orders`.`deleted_at` is null)
 * */
Route::prefix('payments')->group(function () {
    Route::get('epay/pay', [PaymentController::class, 'payPage'])->name('payments.epay.pay');
});


