<?php

use Illuminate\Support\Facades\Route;
use Nichozuo\LaravelCommon\DevTools\Docs\DocsController;
use Nichozuo\LaravelCommon\Helpers\RouteHelper;

Route::middleware('api')->prefix('/api/docs')->name('docs.')->group(function ($router) {
    if (config('app.debug')) {
        RouteHelper::New($router, DocsController::class);
    }
});