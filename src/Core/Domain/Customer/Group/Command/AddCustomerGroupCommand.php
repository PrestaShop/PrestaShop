<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupName;

class AddCustomerGroupCommand
{
    /** @var string[] */
    private $localizedNames;
    /** @var float */
    private $reduction;
    /** @var int */
    private $priceDisplayMethod;
    /** @var bool */
    private $showPrice;

    /**
     * @param string[] $localizedNames
     * @param float $reduction
     * @param int $priceDisplayMethod
     * @param bool $showPrices
     */
    public function __construct(
        array $localizedNames,
        float $reduction,
        int $priceDisplayMethod,
        bool $showPrices
    ) {
        $this->localizedNames = $localizedNames;
        $this->reduction = $reduction;
        $this->priceDisplayMethod = $priceDisplayMethod;
        $this->showPrice = $showPrices;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return int
     */
    public function getPriceDisplayMethod(): int
    {
        return $this->priceDisplayMethod;
    }

    /**
     * @return float
     */
    public function getReduction(): float
    {
        return $this->reduction;
    }

    /**
     * @return bool
     */
    public function isShowPrice(): bool
    {
        return $this->showPrice;
    }
}
