<?php

function coreLoader($object) {
    if (file_exists("core/{$object}.php")) {
        require_once("core/{$object}.php");
    }
}

function libraryLoader($object) {
    if (file_exists("libraries/{$object}.php")) {
        require_once("libraries/{$object}.php");
    }
}

spl_autoload_register('coreLoader');
spl_autoload_register('libraryLoader');
