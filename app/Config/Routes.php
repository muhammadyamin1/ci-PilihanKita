<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');         // Landing page
$routes->get('login', 'Auth::index');     // Form login
$routes->post('login', 'Auth::login');  // Proses login
$routes->group('admin', ['filter' => 'F_admin'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
});
$routes->group('user', ['filter' => 'F_user'], function($routes) {
    $routes->get('pemilihan', 'Auth::pemilihan');
});