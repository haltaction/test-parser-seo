<?php

namespace ParserSeo;

use DOMDocument;
use ParserSeo\Entity\PageInfo;

class SiteAnalyzer
{
    protected $pagesList;

    protected $domain;

    public function __construct()
    {
        $this->pagesList = [];
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Add page to list.
     *
     * @param $url
     *
     * @return PageInfo
     */
    public function addPage($url)
    {
        $url = $this->normalizeLink($url);
        $page = new PageInfo($url);
        $this->pagesList[] = $page;

        return $page;
    }

    /**
     * Add to page list only new URLs.
     *
     * @param array $urlList
     */
    public function addOnlyNewPages(array $urlList)
    {
        foreach ($urlList as $pageUrl) {
            $this->getPage($pageUrl);
        }
    }

    /**
     * Find page in page list by URL.
     *
     * @param $url
     *
     * @return PageInfo
     */
    public function getPage($url)
    {
        $url = $this->normalizeLink($url);
        foreach ($this->pagesList as &$page) {
            if ($page->url === $url) {
                return $page;
            }
        }

        return $this->addPage($url);
    }

    /**
     * @return array
     */
    public function getPageList()
    {
        return $this->pagesList;
    }

    /**
     * Return first page with false value of isParsed.
     *
     * @return mixed|null
     */
    public function getFirstUnparsedPage()
    {
        foreach ($this->pagesList as &$page) {
            if ($page->isParsed === false) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Check if all pages in list marked analyzed.
     *
     * @return bool
     */
    public function isAllPagesAnalyzed()
    {
        foreach ($this->pagesList as $page) {
            if ($page->isParsed === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return array of DOMElements of given tag.
     *
     * @param DOMDocument $pageDOM
     * @param $tagName
     *
     * @return array|bool|null
     */
    public function getElementsByTagName(DOMDocument $pageDOM, $tagName)
    {
        if (empty($pageDOM) || empty($tagName)) {
            return false;
        }

        $tagNodeList = $pageDOM->getElementsByTagName($tagName);
        if ($tagNodeList->length === 0) {
            return null;
        }

        $elements = [];
        foreach ($tagNodeList as $tagNode) {
            $elements[] = $tagNode;
        }

        return $elements;
    }

    /**
     * Return first element by tag, attribute and attribute value.
     *
     * @param DOMDocument $pageDOM
     * @param $tagName
     * @param $attrName
     * @param $attrValue
     *
     * @return \DOMElement|bool|null
     */
    public function getFirstElementByTagAndAttribute(DOMDocument $pageDOM, $tagName, $attrName, $attrValue)
    {
        if (empty($pageDOM) || empty($tagName)) {
            return false;
        }
        $nodeList = $pageDOM->getElementsByTagName($tagName);
        if ($nodeList->length === 0) {
            return null;
        }

        /** @var \DOMElement $node */
        foreach ($nodeList as $node) {
            if ($node->getAttribute($attrName) == $attrValue) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Get length of text content first element in given array.
     *
     * @param array $tags
     *
     * @return int|null
     */
    public function getFirstElementTextLength($tags)
    {
        if (empty($tags)) {
            return null;
        }

        return strlen($tags[0]->textContent);
    }

    /**
     * Concatenate text content from elements in given array and count length of text with or without space symbols.
     *
     * @param $elements
     * @param bool $isExcludeSpaces
     *
     * @return int
     */
    public function countElementsTextLength($elements, $isExcludeSpaces = false)
    {
        if (empty($elements)) {
            return 0;
        }

        $text = '';
        foreach ($elements as $element) {
            $text .= $element->textContent;
        }

        if ($isExcludeSpaces) {
            return strlen(preg_replace('/\s+/', '', $text));
        }

        return strlen($text);
    }

    /**
     * Return only internal links on page.
     *
     * @param $elements
     * @param $domain
     *
     * @return array|int
     */
    public function filterAllInternalLinks($elements, $domain)
    {
        $links = [];
        if (empty($elements)) {
            return $links;
        }

        foreach ($elements as $element) {
            $href = trim($element->getAttribute('href'));
            $href = $this->normalizeLink($href);
            // todo check for duplicate, like pages with # and ?
            if (((substr($href, 0, 1) === '/') && (strlen($href) > 1)) || $this->checkDomainInURL($href, $domain)) {
                $links[] = $href;
            }
        }

        return $links;
    }

    /**
     * Check if given URL with domain.
     *
     * @param $url
     * @param $domain
     *
     * @return bool
     */
    public function checkDomainInURL($url, $domain)
    {
        if (empty(parse_url($url, PHP_URL_SCHEME))) {
            $url = 'http://'.$url;
        }
        $result = strcasecmp(parse_url($url, PHP_URL_HOST), $domain);

        return  $result == 0;
    }

    /**
     * Lead link to common type, like http://domain.com/page.
     *
     * @param $url
     *
     * @return string
     *
     * @throws \Exception
     */
    public function normalizeLink($url)
    {
        if (empty($url)) {
            throw new \Exception('URL must be not empty!');
        }

        // link like //example.com
        if ((substr($url, 0, 2) === '//') && (strlen($url) > 3)) {
            $url = substr($url, 2);
        }
        // relative link
        if ($url[0] === '/') {
            $url = $this->domain.$url;
        }
        if (empty(parse_url($url, PHP_URL_SCHEME))) {
            $url = 'http://'.$url;
        }

        return $url;
    }

    /**
     * @param $pageDOM
     * @param $url
     */
    public function analyzePage($pageDOM, $url)
    {
        $currentPage = $this->getPage($url);
        if (empty($pageDOM)) {
            $currentPage->isParsed = true;

            return;
        }

        // todo refactor it
        $titleElements = $this->getElementsByTagName($pageDOM, 'title');
        $descriptionElement = $this->getFirstElementByTagAndAttribute($pageDOM, 'meta', 'name', 'description');
        $h1Elements = $this->getElementsByTagName($pageDOM, 'h1');
        $imageElements = $this->getElementsByTagName($pageDOM, 'img');
        $linkElements = $this->getElementsByTagName($pageDOM, 'a');
        $paragraphElements = $this->getElementsByTagName($pageDOM, 'p');

        $currentPage->titleLength = $this->getFirstElementTextLength($titleElements);
        $currentPage->descriptionLength = (!empty($descriptionElement)) ? strlen($descriptionElement->getAttribute('content')) : 0;
        $currentPage->h1Length = $this->getFirstElementTextLength($h1Elements);
        $currentPage->imagesCount = count($imageElements);
        $currentPage->linksCount = count($linkElements);
        $currentPage->allTextSymbolsCount = $this->countElementsTextLength($paragraphElements);
        $currentPage->wordsTextSymbolsCount = $this->countElementsTextLength($paragraphElements, true);
        $currentPage->domain = $this->domain;
        $currentPage->isParsed = true;

        $internalLinks = $this->filterAllInternalLinks($linkElements, $this->domain);
        $this->addOnlyNewPages($internalLinks);
    }
}