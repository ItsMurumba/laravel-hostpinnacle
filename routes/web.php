<?php

use Illuminate\Support\Facades\Route;
use Itsmurumba\Hostpinnacle\Http\Controllers\HostpinnacleAccountController;

/*
|--------------------------------------------------------------------------
| Hostpinnacle SaaS Web Routes
|--------------------------------------------------------------------------
|
| Only loaded when hostpinnacle.saas.enabled and hostpinnacle.saas.web_routes_enabled are true.
|
*/

$prefix = config('hostpinnacle.saas.web_prefix', 'hostpinnacle');
$middleware = config('hostpinnacle.saas.web_middleware', ['web', 'auth']);

Route::prefix($prefix)->middleware($middleware)->group(function () {
    Route::resource('accounts', HostpinnacleAccountController::class)->names('hostpinnacle.web.accounts')->except(['create', 'edit']);
});
