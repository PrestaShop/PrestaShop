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
     * @var int
     */
    private $id;

    /**
     * @var array<int, string>
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
     * @var array<int>
     */
    private $shopIds;

    /**
     * @param int $id
     * @param array<int, string> $localizedNames array of names indexed by language id
     * @param DecimalNumber $reduction
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     * @param array<int> $shopIds
     */
    public function __construct(
        int $id,
        array $localizedNames,
        DecimalNumber $reduction,
        bool $displayPriceTaxExcluded,
        bool $showPrice,
        array $shopIds
    ) {
        $this->id = $id;
        $this->localizedNames = $localizedNames;
        $this->reduction = $reduction;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
        $this->shopIds = $shopIds;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
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
    public function displayPriceTaxExcluded(): bool
    {
        return $this->displayPriceTaxExcluded;
    }

    /**
     * @return bool
     */
    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /**
     * @return array<int>
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }
}
