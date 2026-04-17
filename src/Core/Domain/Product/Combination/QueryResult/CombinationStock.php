<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use DateTimeInterface;

class CombinationStock
{
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
     * @var string
     */
    private $location;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @param int $quantity
     * @param int $minimalQuantity
     * @param int $lowStockThreshold
     * @param bool $lowStockAlertEnabled
     * @param string $location
     * @param DateTimeInterface|null $availableDate
     */
    public function __construct(
        int $quantity,
        int $minimalQuantity,
        int $lowStockThreshold,
        bool $lowStockAlertEnabled,
        string $location,
        ?DateTimeInterface $availableDate,
        array $localizedAvailableNowLabels,
        array $localizedAvailableLaterLabels
    ) {
        $this->quantity = $quantity;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertEnabled = $lowStockAlertEnabled;
        $this->availableDate = $availableDate;
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;
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
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
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
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
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
}
