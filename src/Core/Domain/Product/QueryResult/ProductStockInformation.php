<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use DateTimeInterface;

/**
 * Transfers data about Product stock information
 */
class ProductStockInformation
{
    /**
     * @var int
     */
    private $packStockType;

    /**
     * @var int
     */
    private $outOfStockType;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $minimalQuantity;

    /**
     * @var int
     */
    private $lowStockThreshold;

    /**
     * @var bool
     */
    private $lowStockAlertEnabled;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var string
     */
    private $location;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    private ?int $packQuantity;

    /**
     * @param int $packStockType
     * @param int $outOfStockType
     * @param int $quantity
     * @param int $minimalQuantity
     * @param int $lowStockThreshold
     * @param bool $lowStockAlertEnabled
     * @param array $localizedAvailableNowLabels
     * @param array $localizedAvailableLaterLabels
     * @param string $location
     * @param DateTimeInterface|null $availableDate
     * @param int|null $packQuantity
     */
    public function __construct(
        int $packStockType,
        int $outOfStockType,
        int $quantity,
        int $minimalQuantity,
        int $lowStockThreshold,
        bool $lowStockAlertEnabled,
        array $localizedAvailableNowLabels,
        array $localizedAvailableLaterLabels,
        string $location,
        ?DateTimeInterface $availableDate,
        ?int $packQuantity = null,
    ) {
        $this->packStockType = $packStockType;
        $this->outOfStockType = $outOfStockType;
        $this->quantity = $quantity;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertEnabled = $lowStockAlertEnabled;
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;
        $this->availableDate = $availableDate;
        $this->packQuantity = $packQuantity;
    }

    /**
     * @return int
     */
    public function getPackStockType(): int
    {
        return $this->packStockType;
    }

    /**
     * @return int
     */
    public function getOutOfStockType(): int
    {
        return $this->outOfStockType;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getMinimalQuantity(): int
    {
        return $this->minimalQuantity;
    }

    /**
     * @return int
     */
    public function getLowStockThreshold(): int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @return bool
     */
    public function isLowStockAlertEnabled(): bool
    {
        return $this->lowStockAlertEnabled;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableNowLabels(): array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableLaterLabels(): array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }

    /**
     * Dynamic quantity of the pack based on its config and the quantity of its products
     *
     * @return int|null
     */
    public function getPackQuantity(): ?int
    {
        return $this->packQuantity;
    }
}
