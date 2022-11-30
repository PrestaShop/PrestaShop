<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult;

class ShopProductImage
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var bool
     */
    private $isCover;

    public function __construct(
        int $id,
        int $shopId,
        bool $isCover
    ) {
        $this->id = $id;
        $this->shopId = $shopId;
        $this->isCover = $isCover;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }
}
