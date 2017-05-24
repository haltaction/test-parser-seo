<?php

namespace ParserSeo;

use DOMDocument;
use Exception;

class Parser
{
    const TIMEOUT = 30;

    /**
     * Return DOM Document from given URL, if its possible.
     *
     * @param $url
     *
     * @return bool|DOMDocument
     */
    public function getDomFromUrl($url)
    {
        $url = $this->normalizeUrl($url);

        $this->validateUrl($url);

        $htmlString = $this->getHTMLString($url);

        $pageDOM = $this->getDOMFromHTMLString($htmlString);

        return $pageDOM;
    }

    /**
     * Return HTML string of given URL.
     *
     * @param $url
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getHTMLString($url)
    {
        $context = stream_context_create(
            [
                'http' => [
                    'timeout' => self::TIMEOUT,
                ],
            ]
        );
        $htmlString = file_get_contents($url, false, $context);

        if ($htmlString === false) {
            throw new Exception("Error getting content form url '$url'".PHP_EOL);
        }

        return $htmlString;
    }

    /**
     * Add protocol to url, if it not is.
     *
     * @param $url
     *
     * @return string
     */
    protected function normalizeUrl($url)
    {
        $urlScheme = parse_url($url, PHP_URL_SCHEME);
        if (($urlScheme === false) || is_null($urlScheme)) {
            $url = 'http://'.$url;
        }

        return $url;
    }

    /**
     * Validate url.
     *
     * @param $url
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function validateUrl($url)
    {
        // source https://mathiasbynens.be/demo/url-regex
        $pattern = '/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[\/?#]\S*)?$/iuS';

        if (preg_match($pattern, $url) !== 1) {
            throw new Exception("Given URL '$url' is invalid.".PHP_EOL);
        }

        return true;
    }

    /**
     * Get DOM Document from HTML string.
     *
     * @param $string
     *
     * @return bool|DOMDocument
     */
    protected function getDOMFromHTMLString($string)
    {
        if (empty($string)) {
            return false;
        }

        $pageDOM = new DOMDocument();
        $pageDOM->loadHTML($string);

        return $pageDOM;
    }
}