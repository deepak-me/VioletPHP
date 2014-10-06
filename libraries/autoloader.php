<?php
function __autoload($object)
{
require_once("libraries/{$object}.php");
}
