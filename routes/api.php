<?php

use Illuminate\Support\Facades\Route;
use Itsmurumba\Hostpinnacle\Http\Controllers\HostpinnacleAccountController;

/*
|--------------------------------------------------------------------------
| Hostpinnacle SaaS API Routes
|--------------------------------------------------------------------------
|
| Only loaded when hostpinnacle.saas.enabled and hostpinnacle.saas.api_routes_enabled are true.
|
*/

$prefix = config('hostpinnacle.saas.api_prefix', 'api');
$path = config('hostpinnacle.saas.web_prefix', 'hostpinnacle') . '/accounts';
$middleware = config('hostpinnacle.saas.api_middleware', ['api', 'auth:sanctum']);

Route::prefix($prefix)->middleware($middleware)->group(function () use ($path) {
    Route::apiResource($path, HostpinnacleAccountController::class)->names('hostpinnacle.api.accounts');
});
