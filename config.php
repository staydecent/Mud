<?php

error_reporting(E_ALL);
session_start();
date_default_timezone_set('America/Vancouver');

// db (setup for mongo)
define('DB_HOST', 'localhost:27017');
define('MONGO_DB', 'mud_test');
// dirs
define('ROOT', dirname(__FILE__) . '/');
define('MODELS_DIR', ROOT.'models/');
define('TEMPLATE_DIR', ROOT.'templates/');
define('ASSETS_DIR', ROOT.'assets/');
define('CACHE_DIR', ROOT.'cache/');
// domains
define('BASE_DOMAIN', 'locahost/mud');
define('BASE_URL', 'http://'.BASE_DOMAIN.'/');

// URLs: regex => Class Name
$urls = array(
    '/'                     => 'MainHandler',
    'post'                  => 'PostsHandler',
    'post/([a-zA-Z0-9_]+)'  => 'SinglePostHandler',
);