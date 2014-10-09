<?php

require 'config/configs.php';
require 'core/autoloader.php';

//create new router
$router = new Router();

//add new custom routes
$router->addRoute("yes/go", "help","func2");

//custom route with parameters
$router->addRoute("show/result/{param}/{param}","help","myfunc");

//simple routing
$router->addRoute("ok/good", "test", "f2");
$router->addRoute("billu/barber", "help", "func2"); 
$router->addRoute("blog/page/{param}/{param}/sort/{param}/year/{param}", "test", "fourargs"); 
$router->addRoute("work", "test", "f2");
$router->addRoute("category/{param}/year/{param}/month/{param}","test","threeargs"); // changed order from 2 to 1

//initialize the router
$router->initialize();