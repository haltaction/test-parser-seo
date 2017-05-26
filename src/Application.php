<?php

namespace ParserSeo;

class Application
{
    protected $parser;

    protected $siteAnalyzer;

    protected $fileManager;

    protected $sitemapAnalyzer;

    public function __construct($parser, $siteAnalyzer, $fileManager, $sitemapAnalyzer)
    {
        $this->parser = $parser;
        $this->siteAnalyzer = $siteAnalyzer;
        $this->fileManager = $fileManager;
        $this->sitemapAnalyzer = $sitemapAnalyzer;
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

        $sitemapLinks = $this->sitemapAnalyzer->getLinksFromSitemap($domain);
        $this->siteAnalyzer->comparePagesWithSitemapLinks($sitemapLinks);
        $pagesInfoList = $this->siteAnalyzer->getPageList();
        $filePath = $this->fileManager->saveArrayOfObjectsToFile($pagesInfoList, $domain);

        return $filePath;
    }
}