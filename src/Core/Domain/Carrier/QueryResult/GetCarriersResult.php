<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult;

/**
 * Model returning available carriers as well as carriers that have been removed.
 */
class GetCarriersResult
{
    /**
     * @var CarrierSummary[]
     */
    private array $availableCarriers;

    /**
     * @var FilteredCarrier[]
     */
    private array $filteredCarrier;

    public function __construct(array $availableCarriers, array $filteredCarrier)
    {
        $this->availableCarriers = $availableCarriers;
        $this->filteredCarrier = $filteredCarrier;
    }

    /**
     * @return CarrierSummary[]
     */
    public function getAvailableCarriers(): array
    {
        return $this->availableCarriers;
    }

    /**
     * @return FilteredCarrier[]
     */
    public function getFilteredOutCarriers(): array
    {
        return $this->filteredCarrier;
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    public function getAvailableCarriersToArray(): array
    {
        return array_map(function (CarrierSummary $carrier) { return $carrier->toArray(); }, $this->availableCarriers);
    }
}
