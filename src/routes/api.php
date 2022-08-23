<?php

use Illuminate\Support\Facades\Route;
use Nichozuo\LaravelFast\Helpers\RouteHelper;
use Nichozuo\LaravelFast\Http\Controllers\DocsController;

Route::middleware('api')->prefix('/api/docs')->name('docs.')->group(function ($router) {
    if (config('app.debug')) {
        RouteHelper::New($router, DocsController::class);
    }
});