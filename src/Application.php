<?php

namespace ParserSeo;

class Application
{
    protected $parser;

    public function __construct($parser)
    {
        $this->parser = $parser;
    }

    public function parseUrl($url)
    {
//      checkURL
//      getHtlmFromUrl
//      getHTML_DOM
//		analizePage(url)
//		analizeSitemap

        $pageDOM = $this->parser->getDomFromUrl($url);

        // pseudo code
//        $allPages = [];
//        $allPages->add($url);
//
//        do {
//
//            $DOM = $parser->getDomFromUrl($url);
//
//            $pageInfo = $analyzer->analyzePage($DOM);
//            $saver->savePageInfo($pageInfo);
//
//            $otherPages = $analizer->findLinks($DOM);
//            $allPages->markAsParsed($url);
//
//            if (count($otherPages) > 0) {
//                $allPages->add($otherPages);
//            }
//        } while (!$allPages->isAllParsed);
//
//        $allPagesInfo = $saver->getAllPages();
//        $missedPages = $sitemapUrls = $sitemapAnalizer->analize($domain, $allPagesInfo);
//        $saver->markMissedPages($missedPages);

        // just for testing
        return $htmlString;
    }
}