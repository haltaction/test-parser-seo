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
     * @param $url
     * @return PageInfo
     */
    public function addPage($url)
    {
        $page = new PageInfo($url);
        $this->pagesList[] = $page;

        return $page;
    }

    /**
     * Find page in page list by URL.
     * @param $url
     * @return PageInfo
     */
    public function getPage($url)
    {
        foreach ($this->pagesList as &$page) {
            if ($page->url === $url) {
                return $page;
            }
        }

        return $this->addPage($url);
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

    public function analyzePage($pageDOM, $url)
    {
        $titleElements = $this->getElementsByTagName($pageDOM, 'title');
        $descriptionElement = $this->getFirstElementByTagAndAttribute($pageDOM, 'meta', 'name', 'description');
        $h1Elements = $this->getElementsByTagName($pageDOM, 'h1');
        $imageElements = $this->getElementsByTagName($pageDOM, 'img');
        $linkElements = $this->getElementsByTagName($pageDOM, 'a');
        $paragraphElements = $this->getElementsByTagName($pageDOM, 'p');

        $currentPage = $this->getPage($url);
        $currentPage->titleLength = $this->getFirstElementTextLength($titleElements);
        $currentPage->descriptionLength = strlen($descriptionElement->textContent);
        $currentPage->h1Length = $this->getFirstElementTextLength($h1Elements);
        $currentPage->imagesCount = count($imageElements);
        $currentPage->linksCount = count($linkElements);
        $currentPage->allTextSymbolsCount = $this->countElementsTextLength($paragraphElements);
        $currentPage->wordsTextSymbolsCount = $this->countElementsTextLength($paragraphElements, true);

        $currentPage->isParsed = true;

    }
}