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

    public function report($domain)
    {
    }

    public function help()
    {
    }
}