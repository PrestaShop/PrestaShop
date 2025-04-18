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
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class EditCustomerGroupCommand
{
    private GroupId $customerGroupId;

    /**
     * @var string[]|null
     */
    private ?array $localizedNames = null;

    private ?DecimalNumber $reductionPercent = null;

    private ?bool $displayPriceTaxExcluded = null;

    private ?bool $showPrice = null;

    /**
     * @var ShopId[]|null
     */
    private ?array $shopIds = null;

    public function __construct(int $customerGroupId)
    {
        $this->customerGroupId = new GroupId($customerGroupId);
    }

    public function getCustomerGroupId(): GroupId
    {
        return $this->customerGroupId;
    }

    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function getReductionPercent(): ?DecimalNumber
    {
        return $this->reductionPercent;
    }

    public function setReductionPercent(DecimalNumber $reductionPercent): self
    {
        $this->reductionPercent = $reductionPercent;

        return $this;
    }

    public function displayPriceTaxExcluded(): ?bool
    {
        return $this->displayPriceTaxExcluded;
    }

    public function setDisplayPriceTaxExcluded(bool $displayPriceTaxExcluded): self
    {
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;

        return $this;
    }

    public function showPrice(): ?bool
    {
        return $this->showPrice;
    }

    public function setShowPrice(bool $showPrice): self
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    public function getShopIds(): ?array
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     *
     * @return $this
     */
    public function setShopIds(array $shopIds): self
    {
        $this->shopIds = array_map(function (int $shopId) {
            return new ShopId($shopId);
        }, $shopIds);

        return $this;
    }
}
