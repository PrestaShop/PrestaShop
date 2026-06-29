<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class AddCustomerGroupCommand
{
    /** @var string[] */
    private array $localizedNames;

    private DecimalNumber $reductionPercent;

    private bool $displayPriceTaxExcluded;

    private bool $showPrice;

    /** @var ShopId[] */
    private array $shopIds;

    /** @var array<int, DecimalNumber> category id => reduction percent */
    private array $categoryReductions = [];

    /** @var int[] */
    private array $authorizedModuleIds = [];

    /**
     * @param string[] $localizedNames
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
        $this->shopIds = array_map(fn (int $shopId) => new ShopId($shopId), $shopIds);
    }

    /** @return string[] */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function displayPriceTaxExcluded(): bool
    {
        return $this->displayPriceTaxExcluded;
    }

    public function getReductionPercent(): DecimalNumber
    {
        return $this->reductionPercent;
    }

    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /** @return ShopId[] */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    /** @return array<int, DecimalNumber> */
    public function getCategoryReductions(): array
    {
        return $this->categoryReductions;
    }

    /**
     * @param array<int, string|float> $categoryReductions category id => reduction percent
     */
    public function setCategoryReductions(array $categoryReductions): self
    {
        foreach ($categoryReductions as $categoryId => $reduction) {
            $this->categoryReductions[(int) $categoryId] = new DecimalNumber((string) $reduction);
        }

        return $this;
    }

    /** @return int[] */
    public function getAuthorizedModuleIds(): array
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
