<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This class is responsible of managing filters of Listing pages.
 */
class Filters extends ParameterBag implements SearchCriteriaInterface
{
    /** @var string */
    protected $uuid = '';

    /**
     * @param array $filters
     * @param string $uuid
     */
    public function __construct(array $filters = [], $uuid = '')
    {
        parent::__construct($filters);
        $this->uuid = !empty($uuid) ? $uuid : $this->uuid;
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
    public function getOrderBy()
    {
        return $this->get('orderBy');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderWay()
    {
        return $this->get('sortOrder');
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->get('offset');
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->get('limit');
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
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }
}
