<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Search;

final class SearchCriteria implements SearchCriteriaInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * @var null|string
     */
    private $orderBy;

    /**
     * @var null|string
     */
    private $orderWay;

    /**
     * @var null|string
     */
    private $offset;

    /**
     * @var null|string
     */
    private $limit;

    /**
     * @param array $filters
     * @param string|null $orderBy
     * @param string|null $orderWay
     * @param string|null $offset
     * @param string|null $limit
     */
    public function __construct(array $filters, $orderBy = null, $orderWay = null, $offset = null, $limit = null)
    {
        $this->filters = $filters;
        $this->orderBy = $orderBy;
        $this->orderWay = $orderWay;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @return string|null Return order by or null to disable ordering
     */
    public function getOrderBy()
    {
        // TODO: Implement getOrderBy() method.
    }

    /**
     * @return string|null Return order by or null to disable ordering
     */
    public function getOrderWay()
    {
        // TODO: Implement getOrderWay() method.
    }

    /**
     * @return int|null Return offset or null to disable offset
     */
    public function getOffset()
    {
        // TODO: Implement getOffset() method.
    }

    /**
     * @return int|null Return limit or null to disable limiting
     */
    public function getLimit()
    {
        // TODO: Implement getLimit() method.
    }

    /**
     * @return array Return filters
     */
    public function getFilters()
    {
        // TODO: Implement getFilters() method.
    }
}
