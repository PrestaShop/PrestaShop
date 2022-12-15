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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Filters of product combination list.
 *
 * Combination list is handled by javascript so it doesn't need grid id
 */
class ProductCombinationFilters extends Filters
{
    public const LIST_LIMIT = 10;

    private const FILTER_PREFIX = 'product_combinations_';

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $shopId;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $filters = [], $filterId = '')
    {
        parent::__construct($filters, $filterId);
        if (!isset($filters['filters']['product_id'])) {
            throw new InvalidArgumentException(sprintf('%s filters expect a product_id filter', static::class));
        }

        if (!isset($filters['filters']['shop_id'])) {
            throw new InvalidArgumentException(sprintf('%s filters expect a shop_id filter', static::class));
        }

        $this->needsToBePersisted = false;
        $this->productId = (int) $filters['filters']['product_id'];
        $this->shopId = (int) $filters['filters']['shop_id'];

        // Since each combination lists depends on its associated product, the filterId must depend on it so that each
        // has an independent filter saved in database (@see PersistFiltersBuilder and @see RepositoryFiltersBuilder)
        // It will also need to be used as parameter prefix in the request to be correctly fetched by RequestFiltersBuilder
        $this->filterId = static::generateFilterId($this->productId, $this->shopId);
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_product_attribute',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }

    /**
     * @param int $productId
     * @param int $shopId
     *
     * @return string
     */
    public static function generateFilterId(int $productId, int $shopId): string
    {
        return self::FILTER_PREFIX . $productId . '_' . $shopId;
    }
}
