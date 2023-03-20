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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\InvalidReductionException;
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
    private $reduction;

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
     * @param DecimalNumber $reduction
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     * @param ShopId[] $shopIds
     */
    public function __construct(
        array $localizedNames,
        DecimalNumber $reduction,
        bool $displayPriceTaxExcluded,
        bool $showPrice,
        array $shopIds
    ) {
        $this->localizedNames = $localizedNames;
        $this->reduction = $reduction;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
        $this->shopIds = $shopIds;

        $this->assertIsCorrect();
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
    public function getReduction(): DecimalNumber
    {
        return $this->reduction;
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

    private function assertIsCorrect()
    {
        $this->assertReductionIsEqualOrGreaterThanZero();
    }

    private function assertReductionIsEqualOrGreaterThanZero()
    {
        $this->assertIsPrice($this->reduction);
    }

    private function assertIsPrice(DecimalNumber $price)
    {
        $regexCheck = preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/', (string) $price);
        if (0 === $regexCheck || false === $regexCheck) {
            throw new InvalidReductionException();
        }

        if ($price->isNegative()) {
            throw new InvalidReductionException();
        }

        $integerPart = (int) $price->getIntegerPart();
        $fractionalPart = (int) $price->getFractionalPart();
        if ($integerPart < 0 || $integerPart > 100 || ($integerPart === 100 && $fractionalPart > 0)) {
            throw new InvalidReductionException();
        }
    }
}
