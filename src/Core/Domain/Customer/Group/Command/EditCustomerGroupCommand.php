<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class EditCustomerGroupCommand
{
    private CustomerGroupId $customerGroupId;

    /** @var string[]|null */
    private ?array $localizedNames = null;

    private ?DecimalNumber $reductionPercent = null;

    private ?bool $displayPriceTaxExcluded = null;

    private ?bool $showPrice = null;

    /** @var ShopId[]|null */
    private ?array $shopIds = null;

    /** @var array<int, DecimalNumber>|null category id => reduction percent */
    private ?array $categoryReductions = null;

    /** @var int[]|null */
    private ?array $authorizedModuleIds = null;

    public function __construct(int $customerGroupId)
    {
        $this->customerGroupId = new CustomerGroupId($customerGroupId);
    }

    public function getCustomerGroupId(): CustomerGroupId
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
     */
    public function setShopIds(array $shopIds): self
    {
        $this->shopIds = array_map(fn (int $shopId) => new ShopId($shopId), $shopIds);

        return $this;
    }

    /** @return array<int, DecimalNumber>|null */
    public function getCategoryReductions(): ?array
    {
        return $this->categoryReductions;
    }

    /**
     * @param array<int, string|float> $categoryReductions category id => reduction percent
     */
    public function setCategoryReductions(array $categoryReductions): self
    {
        $this->categoryReductions = [];
        foreach ($categoryReductions as $categoryId => $reduction) {
            $this->categoryReductions[(int) $categoryId] = new DecimalNumber((string) $reduction);
        }

        return $this;
    }

    /** @return int[]|null */
    public function getAuthorizedModuleIds(): ?array
    {
        return $this->authorizedModuleIds;
    }

    /**
     * @param int[] $authorizedModuleIds
     */
    public function setAuthorizedModuleIds(array $authorizedModuleIds): self
    {
        $this->authorizedModuleIds = array_map('intval', $authorizedModuleIds);

        return $this;
    }
}
