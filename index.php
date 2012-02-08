<?php

require 'config.php';

// load Mud
require 'libs/mud/Event.php';
require 'libs/mud/App.php';
require 'libs/mud/RequestHandler.php';
require 'libs/mud/Template.php';
require 'libs/mud/MongoModel.php';
require 'libs/mud/Logger.php';
// 3rd party, etc.
require 'libs/h2o-php/h2o.php';
require 'libs/utilities.php';

// start the app
$Mud = new Mud\App;
$Log = Mud\Logger::instance(ROOT.'logs/', Mud\Logger::DEBUG);

// handle dem wildcard subdomains!
$subdomain = str_replace('.'.BASE_DOMAIN, '', $_SERVER["HTTP_HOST"]);

if ($subdomain !== BASE_DOMAIN && $subdomain !== 'www') 
{
    define('SUBDOMAIN', $subdomain);
    $Mud->react(array('(.)' => 'SubdomainHandler')); 
}
else 
{
    $Mud->react($urls);   
}