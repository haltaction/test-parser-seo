<?php

//error_reporting(E_ERROR); // enable for prod

require_once __DIR__ . "/vendor/autoload.php";

use ParserSeo\CommandWrapper;
use ParserSeo\Application;
use ParserSeo\FileManager;
use ParserSeo\Parser;
use ParserSeo\SiteAnalyzer;
use ParserSeo\SitemapAnalyzer;

$parser = new Parser();
$siteAnalyzer = new SiteAnalyzer();
$fileManager = new FileManager();
$sitemapAnalyzer = new SitemapAnalyzer($parser);
$app = new Application(
    $parser,
    $siteAnalyzer,
    $fileManager,
    $sitemapAnalyzer
);
$commands = new CommandWrapper($app);
