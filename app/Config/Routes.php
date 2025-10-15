<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');         // Landing page
$routes->get('login', 'Auth::index');     // Form login
$routes->post('login', 'Auth::login');  // Proses login
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth:admin']);
$routes->get('pemilihan', 'Auth::pemilihan', ['filter' => 'auth:user']);