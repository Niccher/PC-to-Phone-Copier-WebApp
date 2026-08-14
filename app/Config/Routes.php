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

$routes->post('device/log_metrics', 'Device::log_metrics');
$routes->post('device/register', 'Device::device_register');

$routes->group('auth', function ($routes) {
    $routes->add('login', 'Auth::login');
    $routes->add('register', 'Auth::register');
    $routes->add('logout', 'Auth::user_logout');
});

$routes->group('device', function ($routes) {
    $routes->get('ping', 'Device::ping');
    $routes->post('register', 'Device::device_register');
    $routes->post('log_metrics', 'Device::log_metrics');
    $routes->post('metrics', 'Device::log_metrics');
    $routes->add('check', 'Device::check');
    $routes->add('register', 'Device::device_register');
    $routes->add('log_metrics', 'Device::log_metrics');
});

$routes->group('home', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('/', 'Home::home');
    $routes->add('check', 'Home::home_ajax_code_check');
});

$routes->group('home', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('recent', 'Home::home');
    $routes->add('files', 'Utils\TypeFile');
    $routes->add('texts', 'Utils\TypeText');
    $routes->add('trashed', 'Utils\TypeTrash');
});

$routes->group('api', ['filter' => 'session_auth'], function ($routes) {
    $routes->get('events/stream', 'Api\Events::stream');
    $routes->post('restore-file', 'Api\TrashApi::restoreFile');
    $routes->post('restore-text', 'Api\TrashApi::restoreText');
    $routes->post('permanent-delete-file', 'Api\TrashApi::permanentDeleteFile');
    $routes->post('permanent-delete-text', 'Api\TrashApi::permanentDeleteText');
    $routes->post('empty-trash', 'Api\TrashApi::emptyTrash');
});

$routes->group('api/v1', ['filter' => 'device_auth'], function ($routes) {
    $routes->get('ping', 'Api\v1\DeviceApi::ping');
    $routes->post('device/register', 'Api\v1\DeviceApi::register');
    $routes->post('device/metrics', 'Api\v1\DeviceApi::metrics');
    $routes->get('device/sessions', 'Api\v1\AuthApi::sessions');
    $routes->post('auth/pair', 'Api\v1\AuthApi::pair');
    $routes->post('auth/reactivate', 'Api\v1\AuthApi::reactivate');
    
    $routes->get('files', 'Api\v1\FilesApi::list');
    $routes->post('files', 'Api\v1\FilesApi::upload');
    $routes->delete('files/(:segment)', 'Api\v1\FilesApi::delete/$1');
    $routes->post('files/delete', 'Api\v1\FilesApi::delete');

    $routes->get('texts', 'Api\v1\TextsApi::list');
    $routes->post('texts', 'Api\v1\TextsApi::create');
});

$routes->group('saved', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('download/(:any)', 'Download::browser_file_download/$1');
    $routes->add('view/(:any)', 'Download::browser_file_view/$1');
    $routes->add('delete/(:any)', 'Download::browser_file_delete/$1');
});

$routes->group('home', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('file/upload', 'Upload::file_uploaded_by_browser');
    $routes->add('phone/upload', 'Upload::file_uploaded_by_phone');
    $routes->add('phone/text_save', 'Utils\TypeText::text_save');
    $routes->add('phone/get_files_uploaded_by_session', 'Upload::file_uploaded_by_phone_session');
    $routes->add('phone/get_texts_uploaded_by_session', 'Utils\TypeText::text_get_by_session');
    $routes->add('phone/get_files_uploaded_by_session_download', 'Download::file_uploaded_by_phone_session_download');
    $routes->add('phone/set_files_to_delete', 'Download::file_action_delete');
});

$routes->group('text', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('save', 'Utils\TypeText::text_save');
    $routes->add('delete/(:any)', 'Utils\TypeText::text_delete/$1');
    $routes->add('view/(:any)', 'Utils\TypeText::public_view/$1');
});

$routes->group('files', ['filter' => 'session_auth'], function ($routes) {
    $routes->add('search', 'DebugManager::search');
    $routes->add('rename', 'DebugManager::rename');
    $routes->add('add-tag', 'DebugManager::add_tag');
    $routes->add('remove-tag', 'DebugManager::remove_tag');
    $routes->add('update-category', 'DebugManager::update_category');
    $routes->add('update-description', 'DebugManager::update_description');
    $routes->add('batch-delete', 'DebugManager::batch_delete');
    $routes->add('batch-add-tag', 'DebugManager::batch_add_tag');
    $routes->add('batch-download', 'DebugManager::batch_download');
    $routes->add('preview/(:any)', 'DebugManager::get_file_details/$1');
    $routes->add('metadata', 'DebugManager::get_categories_and_tags');
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