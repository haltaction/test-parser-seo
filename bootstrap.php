<?php

//error_reporting(E_ERROR); // enable for prod

require_once __DIR__ . "/vendor/autoload.php";

use ParserSeo\CommandWrapper;
use ParserSeo\Application;
use ParserSeo\Parser;

$app = new Application(new Parser());
$commands = new CommandWrapper($app);
