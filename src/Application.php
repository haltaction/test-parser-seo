<?php

namespace ParserSeo;


class Application
{
    protected $parser;

    protected $siteAnalyzer;

    public function __construct($parser, $siteAnalyzer)
    {
        $this->parser = $parser;
        $this->siteAnalyzer = $siteAnalyzer;
    }

    public function parseUrl($url)
    {
        $this->siteAnalyzer->setDomain($this->parser->getDomainFromUrl($url));
        $this->siteAnalyzer->addPage($url);

        do {
            $currentUrl = $this->siteAnalyzer->getFirstUnparsedPage()->url;
            $pageDOM = $this->parser->getDomFromUrl($currentUrl);
            $this->siteAnalyzer->analyzePage($pageDOM, $currentUrl, $this->parser);

        } while (!$this->siteAnalyzer->isAllPagesAnalyzed());


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
        return true;
    }
}