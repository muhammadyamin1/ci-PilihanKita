<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');         // Landing page

$routes->get('login', 'Auth::index');     // Form login
$routes->post('login', 'Auth::login');  // Proses login
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::processForgotPassword');
$routes->get('forgot-password/verify', 'Auth::forgotPasswordVerify');
$routes->post('forgot-password/verify', 'Auth::processForgotPasswordVerify');

$routes->group('admin', ['filter' => 'F_admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->get('kategori', 'Admin\Kategori::index');
    $routes->post('kategori/store', 'Admin\Kategori::store');
    $routes->post('kategori/toggle/(:num)', 'Admin\Kategori::toggle/$1');
    $routes->post('kategori/delete/(:num)', 'Admin\Kategori::delete/$1');

    $routes->get('calon', 'Admin\Calon::index');
    $routes->post('calon/save', 'Admin\Calon::save');
    $routes->get('calon/get/(:num)', 'Admin\Calon::get/$1');
    $routes->post('calon/update', 'Admin\Calon::update');
    $routes->post('calon/delete/(:num)', 'Admin\Calon::delete/$1');

    $routes->get('pemilih', 'Admin\Pemilih::index');
    $routes->get('pemilih/tambah', 'Admin\Pemilih::create');
    $routes->post('pemilih/simpan', 'Admin\Pemilih::store');
    $routes->get('pemilih/edit/(:num)', 'Admin\Pemilih::edit/$1');
    $routes->post('pemilih/update/(:num)', 'Admin\Pemilih::update/$1');
    $routes->get('pemilih/reset-form/(:num)', 'Admin\Pemilih::resetForm/$1');
    $routes->post('pemilih/reset/(:num)', 'Admin\Pemilih::reset/$1');
    $routes->post('pemilih/hapus/(:num)', 'Admin\Pemilih::hapus/$1');
    $routes->match(['get', 'post'], 'pemilih/generate', 'Admin\Pemilih::generate');
    $routes->post('pemilih/update-nama/(:num)', 'Admin\Pemilih::updateNama/$1');
    $routes->post('pemilih/update-all-nama', 'Admin\Pemilih::updateAllNames');
    $routes->get('pemilih/download-csv/(:any)', 'Admin\Pemilih::downloadCsv/$1');
    $routes->get('pemilih/import', 'Admin\Pemilih::import');
    $routes->post('pemilih/import-process', 'Admin\Pemilih::importProcess');
    $routes->get('pemilih/download-template', 'Admin\Pemilih::downloadTemplate');
    $routes->get('pemilih/import-result/(:any)', 'Admin\Pemilih::importResult/$1');
    $routes->get('pemilih/download-import-csv/(:any)', 'Admin\Pemilih::downloadImportCsv/$1');
    $routes->get('pemilih/download-generated-csv/(:any)', 'Admin\Pemilih::downloadGeneratedCsv/$1');
    
    // Profile routes
    $routes->get('profile', 'Admin\Profile::index');
    $routes->post('profile/update-password', 'Admin\Profile::updatePassword');
    $routes->post('profile/update-foto', 'Admin\Profile::updateFoto');
});
$routes->group('user', ['filter' => 'F_user'], function ($routes) {
    $routes->get('pemilihan', 'Auth::pemilihan');
    $routes->post('vote', 'Auth::vote');
    $routes->get('ubah-password', 'Auth::ubahPassword');
    $routes->post('ubah-password', 'Auth::processUbahPassword');
});

$routes->get('auth/logout', 'Auth::logout');

$routes->get('foto/calon/(:any)', 'UploadController::showCalon/$1', ['filter' => 'F_general']);
$routes->get('foto/user/(:any)', 'UploadController::showUser/$1', ['filter' => 'F_general']);
