<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

class Pagination
{
    /**
     * @var int the total number of pages for this query
     */
    private $pagesCount;

    /**
     * @var int the index of the returned page
     */
    private $page;

    /**
     * @param int $pagesCount
     *
     * @return $this
     */
    public function setPagesCount($pagesCount)
    {
        if (!is_int($pagesCount)) {
            trigger_error(sprintf('Integer value is expected, got `%s`', gettype($pagesCount)), E_USER_NOTICE);
            $pagesCount = (int) $pagesCount;
        }

        $this->pagesCount = $pagesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getPagesCount()
    {
        return $this->pagesCount;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        if (!is_int($page)) {
            trigger_error(sprintf('Integer value is expected, got `%s`', gettype($page)), E_USER_NOTICE);
            $page = (int) $page;
        }

        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param $page
     * @param string $type
     *
     * @return array
     */
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

    /**
     * @return array
     */
    private function buildSpacer()
    {
        return [
            'type' => 'spacer',
            'page' => null,
            'clickable' => false,
            'current' => false,
        ];
    }

    /**
     * @return array
     */
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
