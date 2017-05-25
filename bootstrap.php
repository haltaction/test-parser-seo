<?php

//error_reporting(E_ERROR); // enable for prod

require_once __DIR__ . "/vendor/autoload.php";

use ParserSeo\CommandWrapper;
use ParserSeo\Application;
use ParserSeo\FileManager;
use ParserSeo\Parser;
use ParserSeo\SiteAnalyzer;

$app = new Application(new Parser(), new SiteAnalyzer(), new FileManager());
$commands = new CommandWrapper($app);
