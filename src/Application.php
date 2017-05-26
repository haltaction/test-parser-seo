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

    /**
     * Parsing information about URL, save it in file and return file path.
     *
     * @param $url
     *
     * @return string
     */
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

    /**
     * Return report by domain.
     *
     * @param $domain
     *
     * @return string
     *
     * @throws \Exception
     */
    public function reportByDomain($domain)
    {
        $errorMessage = "Report for domain '".$domain."' not found. Please, run 'parse' command first.";
        $reportMessage = "Report for domain '".$domain."' contain in file ";

        $domain = $this->parser->getDomainFromUrl($domain);
        $reportFile = $this->fileManager->findFileByDomain($domain);

        if ($reportFile === false) {
            throw new \Exception($errorMessage);
        }

        return $reportMessage.$reportFile;
    }
}