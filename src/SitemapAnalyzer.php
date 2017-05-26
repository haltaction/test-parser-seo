<?php

namespace ParserSeo;

class SitemapAnalyzer
{
    protected $linksList = [];

    protected $currentLink = null;

    protected $parser;

    public function __construct($parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return array
     */
    public function getLinkList()
    {
        return $this->linksList;
    }

    /**
     * @param array $links
     */
    protected function addLinksToList(array $links)
    {
        $newLinks = array_diff($links, $this->getLinkList());
        $this->linksList = array_merge($this->getLinkList(), $newLinks);
    }

    /**
     * @return bool|mixed|null
     */
    protected function getNextLink()
    {
        if (is_null($this->currentLink) && isset($this->linksList[0])) {
            return $this->linksList[0];
        }

        $currentIndex = array_search($this->currentLink, $this->linksList);
        if (isset($this->linksList[++$currentIndex])) {
            $this->currentLink = $this->linksList[$currentIndex];

            return $this->currentLink;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isLastLink()
    {
        $currentIndex = array_search($this->currentLink, $this->linksList);
        if ($currentIndex < (count($this->linksList) - 1)) {
            return false;
        }

        return true;
    }

    /**
     * Check if link is link to another sitemap.
     *
     * @param $link
     *
     * @return bool
     */
    public function isSitemapLink($link)
    {
        // todo change to check by parent tag name("sitemap")
        $urlScheme = parse_url($link, PHP_URL_SCHEME);
        if (($urlScheme === false) || is_null($urlScheme)) {
            $link = 'http://'.$link;
        }

        $path = parse_url($link, PHP_URL_PATH);
        if ((substr($path, 1, 7) == 'sitemap') || substr($path, -4, 4) === '.xml') {
            return true;
        }

        return false;
    }

    /**
     * Get all links form given sitemap DOMDocument.
     *
     * @param $sitemapDOM
     *
     * @return array
     */
    public function getLinksFromDOM($sitemapDOM)
    {
        $links = [];
        if (empty($sitemapDOM)) {
            return $links;
        }

        $tagNodeList = $sitemapDOM->getElementsByTagName('loc');
        if ($tagNodeList->length === 0) {
            return $links;
        }

        foreach ($tagNodeList as $tagNode) {
            $links[] = $tagNode->nodeValue;
        }

        return $links;
    }

    /**
     * Filter array of link by given domain.
     *
     * @param array $links
     * @param $domain
     *
     * @return array
     */
    public function filterLinksByDomain(array $links, $domain)
    {
        $links = array_filter($links, function ($link) use ($domain) {
            $host = parse_url($link, PHP_URL_HOST);

            return $host == $domain;
        });

        return $links;
    }

    /**
     * Get all links with given domain from sitemap.
     *
     * @param $domain
     *
     * @return array
     */
    public function getLinksFromSitemap($domain)
    {
        $this->addLinksToList(['http://'.$domain.'/sitemap.xml']);

        do {
            $this->currentLink = $this->getNextLink();

            if ($this->isSitemapLink($this->currentLink)) {
                $sitemapDOM = $this->parser->getDomFromUrl($this->currentLink);
                $links = $this->getLinksFromDOM($sitemapDOM);
                $domainLinks = $this->filterLinksByDomain($links, $domain);
                $this->addLinksToList($domainLinks);
            }
        } while (!$this->isLastLink());

        return $this->getLinkList();
    }
}