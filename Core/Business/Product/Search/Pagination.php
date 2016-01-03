<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class Pagination
{
    /**
     * The total number of pages for this query.
     *
     * @var int
     */
    private $pagesCount;

    /**
     * The index of the returned page.
     *
     * @var int
     */
    private $page;

    public function setPagesCount($pagesCount)
    {
        $this->pagesCount = (int) $pagesCount;

        return $this;
    }

    public function getPagesCount()
    {
        return $this->pagesCount;
    }

    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    private function buildPageLink($page, $type = 'page')
    {
        $current = $page === $this->getPage();

        return [
            'type' => $type,
            'page' => $page,
            'clickable' => !$current,
            'current' => $type === 'page' ? $current : false,
        ];
    }

    private function buildSpacer()
    {
        return [
            'type' => 'spacer',
            'page' => null,
            'clickable' => false,
            'current' => false,
        ];
    }

    public function buildLinks()
    {
        $links = [];

        $addPageLink = function ($page) use (&$links) {
            static $lastPage = null;

            if ($page < 1 || $page > $this->getPagesCount()) {
                return;
            }

            if (null !== $lastPage && $page > $lastPage + 1) {
                $links[] = $this->buildSpacer();
            }

            if ($page !== $lastPage) {
                $links[] = $this->buildPageLink($page);
            }

            $lastPage = $page;
        };

        $boundaryContextLength = 1;
        $pageContextLength = 3;

        $links[] = $this->buildPageLink(max(1, $this->getPage() - 1), 'previous');

        for ($i = 0; $i < $boundaryContextLength; ++$i) {
            $addPageLink(1 + $i);
        }

        $start = max(1, $this->getPage() - (int) floor(($pageContextLength - 1) / 2));
        if ($start + $pageContextLength > $this->getPagesCount()) {
            $start = $this->getPagesCount() - $pageContextLength + 1;
        }

        for ($i = 0; $i < $pageContextLength; ++$i) {
            $addPageLink($start + $i);
        }

        for ($i = 0; $i < $boundaryContextLength; ++$i) {
            $addPageLink($this->getPagesCount() - $boundaryContextLength + 1 + $i);
        }

        $links[] = $this->buildPageLink(min($this->getPagesCount(), $this->getPage() + 1), 'next');

        return $links;
    }
}
