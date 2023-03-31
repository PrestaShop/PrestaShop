<?php
/*
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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class AddCustomerGroupCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var DecimalNumber
     */
    private $reductionPercent;

    /**
     * @var bool
     */
    private $displayPriceTaxExcluded;

    /**
     * @var bool
     */
    private $showPrice;

    /**
     * @var ShopId[]
     */
    private $shopIds;

    /**
     * @param string[] $localizedNames
     * @param DecimalNumber $reductionPercent
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     * @param array<int> $shopIds
     */
    public function __construct(
        array $localizedNames,
        DecimalNumber $reductionPercent,
        bool $displayPriceTaxExcluded,
        bool $showPrice,
        array $shopIds
    ) {
        $this->assertReductionIsValid($reductionPercent);

        $this->localizedNames = $localizedNames;
        $this->reductionPercent = $reductionPercent;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
        $this->shopIds = array_map(function (int $shopId) {
            return new ShopId($shopId);
        }, $shopIds);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return bool
     */
    public function displayPriceTaxExcluded(): bool
    {
        return $this->displayPriceTaxExcluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getReductionPercent(): DecimalNumber
    {
        return $this->reductionPercent;
    }

    /**
     * @return bool
     */
    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /**
     * @return ShopId[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    private function assertReductionIsValid(DecimalNumber $reductionPercent): void
    {
        if ($reductionPercent->isLowerThanZero() || $reductionPercent->isGreaterThan(new DecimalNumber('100'))) {
            throw new GroupConstraintException(
                'Reduction percent must be between 0 and 100',
                GroupConstraintException::INVALID_REDUCTION
            );
        }
    }
}
