<?php

namespace ParserSeo;

class CommandWrapper
{
    public $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }
    /**
     * Start process of parsing url.
     *
     * @param $arguments
     */
    public function parse($arguments)
    {
        $errorMessage = "Parameter 'url' is required!" . PHP_EOL;

        // check param existing
        if (!isset($arguments[1])) {
            exit($errorMessage);
        }

        try {
            $filename = $this->app->parseUrl($arguments[1]);
        } catch (\Exception $e) {
            exit($e->getMessage());
        }

//        $filename = "file.csv" . PHP_EOL;
        exit($filename);
    }

    public function report($domain)
    {

    }

    public function help()
    {

    }
}