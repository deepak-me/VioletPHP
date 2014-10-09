<?php

define('BASE_URL', 'http://localhost/violetphp/'); // must have trailing slash /

/*
 * mysql , postgresql , sqlite
 */

define('DB_TYPE', 'postgresql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'testdb');
define('DB_USER', 'postgres');
define('DB_PASS', 'password');
//for postgresql
define('DB_PORT', '5432');

define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_METHOD', 'index');

define('DEBUG_MODE', 'on');
//turn off debug mode to acrivate error pages
define('PAGE_404', 'error/404.php');

define('TEMPLATE_ENGINE', 'on'); // turn on template engine to use teplate system
