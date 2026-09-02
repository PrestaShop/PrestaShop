<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

class CombinationStockProperties
{
    /**
     * @var StockModification|null
     */
    private $stockModification;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @param StockModification|null $stockModification
     * @param string|null $location
     */
    public function __construct(
        ?StockModification $stockModification = null,
        ?string $location = null
    ) {
        $this->stockModification = $stockModification;
        $this->location = $location;
    }

    /**
     * @return StockModification|null
     */
    public function getStockModification(): ?StockModification
    {
        return $this->stockModification;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }
}
