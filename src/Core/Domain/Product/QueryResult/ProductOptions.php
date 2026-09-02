<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds product options information
 */
class ProductOptions
{
    /**
     * @var string
     */
    private $visibility;

    /**
     * @var bool
     */
    private $availableForOrder;

    /**
     * @var bool
     */
    private $onlineOnly;

    /**
     * @var bool
     */
    private $showPrice;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var bool
     */
    private $showCondition;

    /**
     * @var int
     */
    private $manufacturerId;

    /**
     * @param string $visibility
     * @param bool $availableForOrder
     * @param bool $onlineOnly
     * @param bool $showPrice
     * @param string $condition
     * @param bool $showCondition
     * @param int $manufacturerId
     */
    public function __construct(
        string $visibility,
        bool $availableForOrder,
        bool $onlineOnly,
        bool $showPrice,
        string $condition,
        bool $showCondition,
        int $manufacturerId
    ) {
        $this->visibility = $visibility;
        $this->availableForOrder = $availableForOrder;
        $this->onlineOnly = $onlineOnly;
        $this->showPrice = $showPrice;
        $this->condition = $condition;
        $this->showCondition = $showCondition;
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @return bool
     */
    public function isAvailableForOrder(): bool
    {
        return $this->availableForOrder;
    }

    /**
     * @return bool
     */
    public function isOnlineOnly(): bool
    {
        return $this->onlineOnly;
    }

    /**
     * @return bool
     */
    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function showCondition(): bool
    {
        return $this->showCondition;
    }

    /**
     * @return int
     */
    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }
}
