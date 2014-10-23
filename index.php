<?php

require 'config/configs.php';
require 'core/autoloader.php';

//create new router
$router = new Router();

//add new custom routes url-controller-method-type
$router->addRoute("yes/go", "help", "func2","put,post");

//custom route with parameters
$router->addRoute("show/result/{param}/{param}","help","myfunc","get");

$router->addRoute("Result/Page/{param}", "dbController", "dbTest","get");
//simple routing
$router->addRoute("ok/good", "test", "f2","post");
$router->addRoute("billu/barber", "help", "func2","get"); 

//RESTful Routing
$router->addRoute("restful/put", "test", "rest", "put");

$router->addRoute("blog/page/{param}/{param}/sort/{param}/year/{param}", "test", "fourargs","any"); 
$router->addRoute("work", "test", "f2","get");
$router->addRoute("category/{param}/year/{param}/month/{param}","test","threeargs","get"); // changed order from 2 to 1

//initialize the router
$router->initialize();