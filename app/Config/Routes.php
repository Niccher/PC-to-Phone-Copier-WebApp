<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
#$routes->get('/', 'Home::index');
$routes->get('/', 'Auth::landing');

$routes->group('auth', function ($routes) {
    $routes->add('login', 'Auth::login');
    $routes->add('register', 'Auth::register');
    $routes->add('logout', 'Auth::user_logout');
});

$routes->group('home', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('/', 'Home::home');
    $routes->add('recent', 'Home::home');
    $routes->add('files', 'Utils\TypeFile');
    $routes->add('texts', 'Utils\TypeText');
    $routes->add('trashed', 'Utils\TypeTrash');
});

// Versioned REST API v1
$routes->group('api/v1', function ($routes) {
    // Public Auth & Pairing Verification
    $routes->add('auth/check', 'Home::home_ajax_code_check');

    // Device Management & Pairing (device_auth filter handles exemptions internally)
    $routes->group('', ['filter' => 'device_auth'], function ($routes) {
        $routes->get('ping', 'Api\v1\DeviceApi::ping');
        $routes->get('device/ping', 'Api\v1\DeviceApi::ping');
        $routes->post('device/register', 'Api\v1\DeviceApi::register');
        $routes->post('device/metrics', 'Api\v1\DeviceApi::metrics');
        $routes->get('device/sessions', 'Api\v1\AuthApi::sessions');
        $routes->post('auth/pair', 'Api\v1\AuthApi::pair');
        $routes->post('auth/reactivate', 'Api\v1\AuthApi::reactivate');
        $routes->post('auth/session-status', 'Api\v1\AuthApi::sessionStatus');

        $routes->get('analytics/summary', 'Api\v1\AnalyticsApi::summary');
        $routes->post('analytics/summary', 'Api\v1\AnalyticsApi::summary');
        $routes->post('telemetry/event', 'Api\v1\AnalyticsApi::logEvent');

        $routes->get('files', 'Api\v1\FilesApi::list');
        $routes->post('files/list', 'Api\v1\FilesApi::list');
        $routes->post('files', 'Api\v1\FilesApi::upload');
        $routes->post('files/download', 'Api\v1\FilesApi::download');
        $routes->delete('files/(:segment)', 'Api\v1\FilesApi::delete/$1');
        $routes->post('files/delete', 'Api\v1\FilesApi::delete');
        $routes->post('files/batch-delete', 'Api\v1\FilesApi::batchDelete');
        $routes->get('uploaded', 'Api\v1\FilesApi::uploaded');
        $routes->post('uploaded', 'Api\v1\FilesApi::uploaded');
        $routes->post('uploaded/list', 'Api\v1\FilesApi::uploaded');

        $routes->get('texts', 'Api\v1\TextsApi::list');
        $routes->post('texts/list', 'Api\v1\TextsApi::list');
        $routes->post('texts', 'Api\v1\TextsApi::create');
        $routes->post('texts/delete', 'Api\v1\TextsApi::delete');
    });

    // Session-authenticated Web API Endpoints
    $routes->group('', ['filter' => 'session_auth'], function ($routes) {
        // SSE Realtime Stream
        $routes->get('events/stream', 'Api\Events::stream');

        // File Management
        $routes->post('files/upload', 'Upload::file_uploaded_by_browser');
        $routes->get('files/search', 'FileManager::search');
        $routes->get('files/metadata', 'FileManager::get_categories_and_tags');
        $routes->post('files/rename', 'FileManager::rename');
        $routes->post('files/add-tag', 'FileManager::add_tag');
        $routes->post('files/remove-tag', 'FileManager::remove_tag');
        $routes->post('files/update-category', 'FileManager::update_category');
        $routes->post('files/update-description', 'FileManager::update_description');
        $routes->post('files/batch-add-tag', 'FileManager::batch_add_tag');
        $routes->post('files/batch-download', 'FileManager::batch_download');
        $routes->get('files/preview/(:segment)', 'FileManager::get_file_details/$1');

        // Text Management
        $routes->post('texts/save', 'Utils\TypeText::text_save');
        $routes->get('texts/delete/(:segment)', 'Utils\TypeText::text_delete/$1');
        $routes->post('texts/delete/(:segment)', 'Utils\TypeText::text_delete/$1');

        // Trash Management
        $routes->post('trash/files/restore', 'Api\TrashApi::restoreFile');
        $routes->post('trash/texts/restore', 'Api\TrashApi::restoreText');
        $routes->post('trash/files/delete', 'Api\TrashApi::permanentDeleteFile');
        $routes->post('trash/texts/delete', 'Api\TrashApi::permanentDeleteText');
        $routes->post('trash/empty', 'Api\TrashApi::emptyTrash');
    });
});

$routes->group('saved', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('download-all-zip', 'Download::download_all_session_zip');
    $routes->add('download/(:any)', 'Download::browser_file_download/$1');
    $routes->add('view/(:any)', 'Download::browser_file_view/$1');
    $routes->add('delete/(:any)', 'Download::browser_file_delete/$1');
});

$routes->get('setup/text-tables', 'DatabaseSetup::createTextTables');
$routes->get('debug/info', 'Debug::info');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}