<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult;

/**
 * Model representing carriers that have been removed, along with the products responsible for their removal.
 */
class FilteredCarrier
{
    /**
     * @var ProductSummary[]
     */
    private array $products;

    private CarrierSummary $carrier;

    public function __construct(array $products, CarrierSummary $carrier)
    {
        $this->products = $products;
        $this->carrier = $carrier;
    }

    /**
     * @return ProductSummary[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCarrier(): CarrierSummary
    {
        return $this->carrier;
    }
}
