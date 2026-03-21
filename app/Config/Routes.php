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
// $routes->setAutoRoute(false);

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

$routes->group('device', function ($routes) {
    $routes->add('check', 'Device::check');
    $routes->add('register', 'Device::device_register');
});

$routes->group('home', function ($routes) {
    $routes->add('/', 'Home::home');
    $routes->add('check', 'Home::home_ajax_code_check');
});

$routes->group('home', function ($routes) {
    $routes->add('recent', 'Home::home');
    $routes->add('files', 'Utils\TypeFile');
    $routes->add('texts', 'Utils\TypeText');
    $routes->add('trashed', 'Utils\TypeTrash');
});

// API routes for trash management
$routes->group('api', function ($routes) {
    $routes->post('restore-file', 'Api\TrashApi::restoreFile');
    $routes->post('restore-text', 'Api\TrashApi::restoreText');
    $routes->post('permanent-delete-file', 'Api\TrashApi::permanentDeleteFile');
    $routes->post('permanent-delete-text', 'Api\TrashApi::permanentDeleteText');
    $routes->post('empty-trash', 'Api\TrashApi::emptyTrash');
});

$routes->group('saved', function ($routes) {
    $routes->add('download/(:any)', 'Download::browser_file_download/$1');
    $routes->add('delete/(:any)', 'Download::browser_file_delete/$1');
});

$routes->group('home', function ($routes) {
    $routes->add('file/upload', 'Upload::file_uploaded_by_browser');
    $routes->add('phone/upload', 'Upload::file_uploaded_by_phone');
    $routes->add('phone/get_files_uploaded_by_session', 'Upload::file_uploaded_by_phone_session');
    $routes->add('phone/get_files_uploaded_by_session_download', 'Download::file_uploaded_by_phone_session_download');
    $routes->add('phone/set_files_to_delete', 'Download::file_action_delete');
});

$routes->group('text', function ($routes) {
    $routes->add('save', 'Utils\TypeText::text_save');
    $routes->add('delete/(:any)', 'Utils\TypeText::text_delete/$1');
    $routes->add('view/(:any)', 'Utils\TypeText::public_view/$1');
});

$routes->group('files', function ($routes) {
    $routes->add('search', 'FileManager::search');
    $routes->add('rename', 'FileManager::rename');
    $routes->add('add-tag', 'FileManager::add_tag');
    $routes->add('remove-tag', 'FileManager::remove_tag');
    $routes->add('update-category', 'FileManager::update_category');
    $routes->add('update-description', 'FileManager::update_description');
    $routes->add('batch-delete', 'FileManager::batch_delete');
    $routes->add('batch-add-tag', 'FileManager::batch_add_tag');
    $routes->add('batch-download', 'FileManager::batch_download');
    $routes->add('details/(:any)', 'FileManager::get_file_details/$1');
    $routes->add('metadata', 'FileManager::get_categories_and_tags');
});

$routes->get('setup/text-tables', 'DatabaseSetup::createTextTables');

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