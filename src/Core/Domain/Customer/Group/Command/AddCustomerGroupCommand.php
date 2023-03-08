<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;

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
     * @param string[] $localizedNames
     * @param DecimalNumber $reduction
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     */
    public function __construct(
        array $localizedNames,
        DecimalNumber $reduction,
        bool $displayPriceTaxExcluded,
        bool $showPrice
    ) {
        $this->localizedNames = $localizedNames;
        $this->reduction = $reduction;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
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
}
