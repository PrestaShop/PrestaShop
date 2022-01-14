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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

class StockMovementHistorySettings
{
    /**
     * @var StockMovementFilter Filter to select stock movements as usual
     */
    protected $mainFilter;

    /**
     * @var StockMovementFilter Filter to exclude single stock movements from groupings
     */
    protected $singleFilter;

    /**
     * @var bool Excludes groupings with zero quantity
     */
    protected $isZeroQuantityGroupingExcluded = false;

    public function __construct()
    {
        $this->mainFilter = new StockMovementFilter();
        $this->singleFilter = new StockMovementFilter();
    }

    public function getMainFilter(): StockMovementFilter
    {
        return $this->mainFilter;
    }

    public function getSingleFilter(): StockMovementFilter
    {
        return $this->singleFilter;
    }

    public function isZeroQuantityGroupingExcluded(): bool
    {
        return $this->isZeroQuantityGroupingExcluded;
    }

    public function excludeGroupingWithZeroQuantity(bool $excluded): self
    {
        $this->isZeroQuantityGroupingExcluded = $excluded;

        return $this;
    }
}
