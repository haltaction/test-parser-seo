<?php

namespace ParserSeo;

class Application
{
    protected $parser;

    protected $siteAnalyzer;

    protected $fileManager;

    public function __construct($parser, $siteAnalyzer, $fileManager)
    {
        $this->parser = $parser;
        $this->siteAnalyzer = $siteAnalyzer;
        $this->fileManager = $fileManager;
    }

    public function parseUrl($url)
    {
        $this->fileManager->checkDirPermission();

        $domain = $this->parser->getDomainFromUrl($url);
        $this->siteAnalyzer->setDomain($domain);
        $this->siteAnalyzer->addPage($url);

        do {
            $currentUrl = $this->siteAnalyzer->getFirstUnparsedPage()->url;
            $pageDOM = $this->parser->getDomFromUrl($currentUrl);
            $this->siteAnalyzer->analyzePage($pageDOM, $currentUrl, $this->parser);
        } while (!$this->siteAnalyzer->isAllPagesAnalyzed());

        $pagesInfoList = $this->siteAnalyzer->getPageList();
        $filePath = $this->fileManager->saveArrayOfObjectsToFile($pagesInfoList, $domain);

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

        return $filePath;
    }
}