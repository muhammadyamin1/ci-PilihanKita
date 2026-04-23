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
    $routes->post('pemilih/hapus/(:num)', 'Admin\Pemilih::hapus/$1');
    $routes->match(['get', 'post'], 'pemilih/generate', 'Admin\Pemilih::generate');
    $routes->post('pemilih/update-nama/(:num)', 'Admin\Pemilih::updateNama/$1');
    $routes->post('pemilih/update-all-nama', 'Admin\Pemilih::updateAllNames');
    $routes->get('pemilih/download-csv/(:any)', 'Admin\Pemilih::downloadCsv/$1');
});
$routes->group('user', ['filter' => 'F_user'], function ($routes) {
    $routes->get('pemilihan', 'Auth::pemilihan');
});

$routes->get('auth/logout', 'Auth::logout');

$routes->get('foto/calon/(:any)', 'UploadController::showCalon/$1', ['filter' => 'F_general']);
