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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * This query returns a list of stock movements for a combination, each row is either
 * an edition from the BO by an employee or a range of customer orders resume (all the
 * combinations that were sold between each edition).
 */
class GetCombinationStockMovements
{
    public const DEFAULT_LIMIT = 5;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @throws CombinationConstraintException
     * @throws ShopException
     * @throws LogicException
     */
    public function __construct(
        int $combinationId,
        int $shopId,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ) {
        $this->shopId = new ShopId($shopId);

        if ($offset < 0) {
            throw new InvalidArgumentException('Offset should be a positive integer');
        }
        $this->offset = $offset;

        if ($limit < 0) {
            throw new InvalidArgumentException('Limit should be a positive integer');
        }
        $this->limit = $limit;
        $this->combinationId = new CombinationId($combinationId);
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }
}
