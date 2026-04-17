<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult;

/**
 * Transfer SpecificPrice list data
 */
class SpecificPriceList
{
    /**
     * @var SpecificPriceForListing[]
     */
    private $specificPrices;

    /**
     * @var int
     */
    private $totalSpecificPricesCount;

    /**
     * @param SpecificPriceForListing[] $specificPrices
     * @param int $totalSpecificPricesCount
     */
    public function __construct(
        array $specificPrices,
        int $totalSpecificPricesCount
    ) {
        $this->specificPrices = $specificPrices;
        $this->totalSpecificPricesCount = $totalSpecificPricesCount;
    }

    /**
     * @return SpecificPriceForListing[]
     */
    public function getSpecificPrices(): array
    {
        return $this->specificPrices;
    }

    /**
     * @return int
     */
    public function getTotalSpecificPricesCount(): int
    {
        return $this->totalSpecificPricesCount;
    }
}
