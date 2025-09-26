<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');         // Landing page
$routes->get('login', 'Auth::index');     // Form login
$routes->post('login', 'Auth::process');  // Proses login
$routes->get('dashboard', 'Auth::dashboard');
$routes->get('pemilihan', 'Auth::pemilihan');