<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\BiolinkAppearanceController;
use App\Http\Controllers\BiolinkContentConfigController;
use App\Http\Controllers\BiolinkContentOrderController;
use App\Http\Controllers\BiolinkController;
use App\Http\Controllers\CsvExportController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\LinkReportsController;
use App\Http\Controllers\LinkUsageController;
use Common\Auth\Controllers\GetAccessTokenController;
use Common\Auth\Controllers\RegisterController;

Route::group(['prefix' => 'v1'], function() {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // LINK
        Route::post('link/batch/shorten', [LinkController::class, 'storeBatch']);
        Route::get('link/analytics', [LinkReportsController::class, 'show'])->middleware('auth');
        Route::get('link/usage', [LinkUsageController::class, 'getUsage'])->middleware('auth');
        Route::apiResource('link', 'LinkController', ['as' => 'apiLinks']);

        // LINK GROUP
        Route::apiResource('link-group', 'LinkGroupController', ['as' => 'apiGroups']);
        Route::get('link-group/{linkGroup}/links', 'LinkGroupController@links');
        Route::get('link-group/{linkGroup}/analytics', 'LinkGroupController@analytics');
        Route::post('link-group/{linkGroup}/detach', 'LinkGroupAttachmentsController@detach');
        Route::post('link-group/{linkGroup}/attach', 'LinkGroupAttachmentsController@attach');

        // BIOLINKS
        Route::post('biolink/{biolink}/update-content-config', [BiolinkContentConfigController::class, 'update']);
        Route::post('biolink/{biolink}/change-order', [BiolinkContentOrderController::class, 'changeOrder']);
        Route::post('biolink/{biolink}/appearance', [BiolinkAppearanceController::class, 'save']);
        Route::apiResource('biolink', 'BiolinkController', ['as' => 'apiBiolinks']);
        Route::get('biolink/{biolink}/analytics', 'BiolinkController@analytics');
        Route::post('biolink/{biolink}/detach', [BiolinkController::class, 'detachContent']);
        Route::apiResource('biolink/{biolink}/widget', 'BiolinkWidgetsController', ['as' => 'apiWidgets']);

        // LINK OVERLAY
        Route::apiResource('link-overlay', 'LinkOverlayController', ['as' => 'apiOverlays']);

        // TRACKING PIXEL
        Route::apiResource('pixel', 'TrackingPixelController', ['as' => 'apiPixels']);

        // LINK PAGES
        Route::apiResource('link-page', 'LinkPagesController', ['as' => 'apiPages']);

        // CSV EXPORT
        Route::post('link/csv/export', [CsvExportController::class, 'exportLinks']);

        // CUSTOM DOMAIN
        Route::group(['middleware' => 'customDomainsEnabled'], function() {
            Route::apiResource('custom-domain', '\Common\Domains\CustomDomainController', ['as' => 'apiDomains']);
            Route::post('custom-domain/authorize/{method}', '\Common\Domains\CustomDomainController@authorizeCrupdate')->where('method', 'store|update');
        });
    });

    // AUTH
    Route::post('auth/register', [RegisterController::class, 'register']);
    Route::post('auth/login', [GetAccessTokenController::class, 'login']);
    Route::get('auth/social/{provider}/callback', '\Common\Auth\Controllers\SocialAuthController@loginCallback');
    Route::post('auth/password/email', '\Common\Auth\Controllers\SendPasswordResetEmailController@sendResetLinkEmail');
});
