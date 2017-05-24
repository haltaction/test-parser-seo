<?php

namespace ParserSeo\Entity;

class PageInfo
{
    public $url;
    public $domain;
    public $titleLength;
    public $descriptionLength;
    public $h1Length;
    public $imagesCount;
    public $linksCount;
    public $allTextSymbolsCount;
    public $wordsTextSymbolsCount;
    public $isParsed;
    public $isInSitemap;

    public function __construct($url)
    {
        $this->url = $url;
        $this->isParsed = false;
        $this->isInSitemap = false;
    }
}