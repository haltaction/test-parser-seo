<?php

namespace ParserSeo;

class CommandWrapper
{
    public $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function formatOutputString($string, $colorCode = 31)
    {
        $formattedString = "\033[".$colorCode.'m'.$string.PHP_EOL."\033[0m";

        return $formattedString;
    }

    /**
     * Start process of parsing url.
     *
     * @param $arguments
     */
    public function parse($arguments)
    {
        $errorMessage = "Parameter 'url' is required!";

        // check param existing
        if (!isset($arguments[1])) {
            exit($this->formatOutputString($errorMessage, 31));
        }

        try {
            $filename = $this->app->parseUrl($arguments[1]);
        } catch (\Exception $e) {
            exit($this->formatOutputString($e->getMessage(), 31));
        }

        exit('Result file - '.$filename.PHP_EOL);
    }

    /**
     * Check if exists report file by domain.
     *
     * @param $arguments
     */
    public function report($arguments)
    {
        $errorMessage = "Parameter 'domain' is required!";
        if (!isset($arguments[1])) {
            exit($this->formatOutputString($errorMessage, 31));
        }

        try {
            $report = $this->app->reportByDomain($arguments[1]);
        } catch (\Exception $e) {
            exit($this->formatOutputString($e->getMessage(), 31));
        }

        exit($report.PHP_EOL);
    }

    /**
     * Display help.
     */
    public function help()
    {
        $helpString = "
Commands:

 'parse site.com' - start parsing given url, find and parse all internal link on pages. Also parsing 'sitemap.xml' and   compare parsed links with sitemap links. Save result in .csv file and print path to it file.
 
 'report site.com' - search report file by given domain. Return path to result .csv file.
 
 'help' - display this text.
";


        exit($helpString.PHP_EOL);
    }
}