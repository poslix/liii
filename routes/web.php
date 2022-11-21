<?php

use App\Http\Controllers\BiolinkAppearanceController;
use App\Http\Controllers\BiolinkContentConfigController;
use App\Http\Controllers\BiolinkContentOrderController;
use App\Http\Controllers\BiolinkController;
use App\Http\Controllers\CsvExportController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\LinkReportsController;
use App\Http\Controllers\LinkUsageController;

Route::group(['prefix' => 'secure'], function () {
    // BOOTSTRAP
    Route::get('bootstrap-data', '\Common\Core\Controllers\BootstrapController@getBootstrapData')->middleware('redirectLink');

    // HOMEPAGE STATS
    Route::get('homepage/stats', 'HomepageStatsController@getStats');

    // LINK
    Route::post('link/batch/shorten', [LinkController::class, 'storeBatch']);
    Route::get('link/analytics', [LinkReportsController::class, 'show'])->middleware('auth');
    Route::get('link/usage', [LinkUsageController::class, 'getUsage'])->middleware('auth');
    Route::apiResource('link', 'LinkController');

    // LINK GROUP
    Route::apiResource('link-group', 'LinkGroupController');
    Route::get('link-group/{linkGroup}/links', 'LinkGroupController@links');
    Route::get('link-group/{linkGroup}/analytics', 'LinkGroupController@analytics');
    Route::post('link-group/{linkGroup}/detach', 'LinkGroupAttachmentsController@detach');
    Route::post('link-group/{linkGroup}/attach', 'LinkGroupAttachmentsController@attach');

    // BIOLINKS
    Route::post('biolink/{biolink}/update-content-config', [BiolinkContentConfigController::class, 'update']);
    Route::post('biolink/{biolink}/change-order', [BiolinkContentOrderController::class, 'changeOrder']);
    Route::post('biolink/{biolink}/appearance', [BiolinkAppearanceController::class, 'save']);
    Route::apiResource('biolink', 'BiolinkController');
    Route::get('biolink/{biolink}/analytics', 'BiolinkController@analytics');
    Route::post('biolink/{biolink}/detach', [BiolinkController::class, 'detachContent']);
    Route::apiResource('biolink/{biolink}/widget', 'BiolinkWidgetsController');

    // LINK OVERLAY
    Route::apiResource('link-overlay', 'LinkOverlayController');

    // TRACKING PIXEL
    Route::apiResource('pixel', 'TrackingPixelController');

    // LINK PAGES
    Route::apiResource('link-page', 'LinkPagesController');

    // CSV EXPORT
    Route::post('link/csv/export', [CsvExportController::class, 'exportLinks']);
});

Route::get('{linkHash}/img', 'LinkImageController@show');

// FRONT-END ROUTES THAT NEED TO BE PRE-RENDERED

// CATCH ALL ROUTES AND REDIRECT TO HOME
Route::get('{all}', '\Common\Core\Controllers\HomeController@show')
    ->where('all', '.*')
    ->middleware('prerenderIfCrawler:homepage')
    ->middleware('redirectLink');
