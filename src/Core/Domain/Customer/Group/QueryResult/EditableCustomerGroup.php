<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

class EditableCustomerGroup
{
    /**
     * @param int $id
     * @param array<int, string> $localizedNames array of names indexed by language id
     * @param DecimalNumber $reduction
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     * @param array<int> $shopIds
     * @param array<int, array{name: string, reduction: DecimalNumber}> $categoryReductions category id => {name, reduction percent}
     * @param int[] $authorizedModuleIds
     */
    public function __construct(
        private readonly int $id,
        private readonly array $localizedNames,
        private readonly DecimalNumber $reduction,
        private readonly bool $displayPriceTaxExcluded,
        private readonly bool $showPrice,
        private readonly array $shopIds,
        private readonly array $categoryReductions = [],
        private readonly array $authorizedModuleIds = [],
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /** @return array<int, string> */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getReduction(): DecimalNumber
    {
        return $this->reduction;
    }

    public function displayPriceTaxExcluded(): bool
    {
        return $this->displayPriceTaxExcluded;
    }

    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /** @return array<int> */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    /** @return array<int, array{name: string, reduction: DecimalNumber}> */
    public function getCategoryReductions(): array
    {
        return $this->categoryReductions;
    }

    /** @return int[] */
    public function getAuthorizedModuleIds(): array
    {
        return $this->authorizedModuleIds;
    }
}
