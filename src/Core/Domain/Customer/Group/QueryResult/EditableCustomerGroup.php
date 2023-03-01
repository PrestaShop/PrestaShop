<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class EditableCustomerGroup
{
    /**
     * @var GroupId
     */
    private $id;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var float
     */
    private $reduction;

    /**
     * @var int
     */
    private $priceDisplayMethod;

    /**
     * @var bool
     */
    private $showPrice;

    /**
     * @param GroupId $id
     * @param string[] $name
     * @param float $reduction
     * @param int $priceDisplayMethod
     * @param bool $showPrice
     */
    public function __construct(
        GroupId $id,
        array   $name,
        float   $reduction,
        int     $priceDisplayMethod,
        bool    $showPrice
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->reduction = $reduction;
        $this->priceDisplayMethod = $priceDisplayMethod;
        $this->showPrice = $showPrice;
    }

    /**
     * @return GroupId
     */
    public function getId(): GroupId
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
     * @return float
     */
    public function getReduction(): float
    {
        return $this->reduction;
    }

    /**
     * @return int
     */
    public function getPriceDisplayMethod(): int
    {
        return $this->priceDisplayMethod;
    }

    /**
     * @return bool
     */
    public function isShowPrice(): bool
    {
        return $this->showPrice;
    }
}
