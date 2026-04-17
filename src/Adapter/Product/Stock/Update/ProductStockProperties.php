<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

class ProductStockProperties
{
    /**
     * @var StockModification|null
     */
    private $stockModification;

    /**
     * @var OutOfStockType|null
     */
    private $outOfStockType;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @param StockModification|null $stockModification
     * @param OutOfStockType|null $outOfStockType
     * @param string|null $location
     */
    public function __construct(
        ?StockModification $stockModification = null,
        ?OutOfStockType $outOfStockType = null,
        ?string $location = null
    ) {
        $this->stockModification = $stockModification;
        $this->outOfStockType = $outOfStockType;
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
     * @return OutOfStockType|null
     */
    public function getOutOfStockType(): ?OutOfStockType
    {
        return $this->outOfStockType;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }
}
