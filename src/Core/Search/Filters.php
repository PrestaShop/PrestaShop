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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Validate;

/**
 * This class is responsible for managing filters of Listing pages.
 */
class Filters extends ParameterBag implements SearchCriteriaInterface
{
    public const LIST_LIMIT = 10;

    /** @var string */
    protected $filterId = '';

    /** @var bool */
    protected $needsToBePersisted = true;

    /**
     * @param array $filters
     * @param string $filterId
     */
    public function __construct(array $filters = [], $filterId = '')
    {
        parent::__construct($filters);
        $this->filterId = !empty($filterId) ? $filterId : $this->filterId;
    }

    /**
     * @return Filters
     */
    public static function buildDefaults()
    {
        return new static(static::getDefaults());
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => static::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => null,
            'sortOrder' => null,
            'filters' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBy()
    {
        $orderBy = $this->get('orderBy');
        if (!Validate::isOrderBy($orderBy)) {
            return null;
        }

        return $orderBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderWay()
    {
        $orderWay = $this->get('sortOrder');
        if (!Validate::isOrderWay($orderWay)) {
            return null;
        }

        return $orderWay;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->getInt('offset') ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->getInt('limit') ?: null;
    }

    /**
     * @param array $parameters
     */
    public function addFilter(array $parameters = [])
    {
        $filters = array_replace($this->getFilters(), $parameters);
        $this->set('filters', $filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->get('filters');
    }

    /**
     * @return string
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * @param string $filterId
     *
     * @return $this
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * @return bool
     */
    public function needsToBePersisted(): bool
    {
        return $this->needsToBePersisted;
    }

    /**
     * @param bool $needsToBePersisted
     *
     * @return static
     */
    public function setNeedsToBePersisted(bool $needsToBePersisted): self
    {
        $this->needsToBePersisted = $needsToBePersisted;

        return $this;
    }
}
