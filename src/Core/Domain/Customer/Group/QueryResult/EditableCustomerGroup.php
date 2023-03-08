<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

class EditableCustomerGroup
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string[]
     */
    private $name;

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
     * @param int $id
     * @param string[] $name
     * @param DecimalNumber $reduction
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     */
    public function __construct(
        int $id,
        array $name,
        DecimalNumber $reduction,
        bool $displayPriceTaxExcluded,
        bool $showPrice
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->reduction = $reduction;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
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
    public function getName(): array
    {
        return $this->name;
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
}
