<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
