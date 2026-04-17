<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Holds information of combination prices
 */
class CombinationPrices
{
    /**
     * @var DecimalNumber
     */
    private $impactOnPrice;

    /**
     * @var DecimalNumber
     */
    private $impactOnPriceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $impactOnUnitPrice;

    /**
     * @var DecimalNumber
     */
    private $impactOnUnitPriceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $ecotax;

    /**
     * @var DecimalNumber
     */
    private $ecotaxTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $wholesalePrice;

    /**
     * Value between 0 and 100.
     *
     * @var DecimalNumber
     */
    private $productTaxRate;

    /**
     * @var DecimalNumber
     */
    private $productPrice;

    /**
     * @var DecimalNumber
     */
    private $productEcotax;

    public function __construct(
        DecimalNumber $impactOnPrice,
        DecimalNumber $impactOnPriceTaxIncluded,
        DecimalNumber $impactOnUnitPrice,
        DecimalNumber $impactOnUnitPriceTaxIncluded,
        DecimalNumber $ecotax,
        DecimalNumber $ecotaxTaxIncluded,
        DecimalNumber $wholesalePrice,
        DecimalNumber $productTaxRate,
        DecimalNumber $productPrice,
        DecimalNumber $productEcotax
    ) {
        $this->impactOnPrice = $impactOnPrice;
        $this->impactOnPriceTaxIncluded = $impactOnPriceTaxIncluded;
        $this->impactOnUnitPrice = $impactOnUnitPrice;
        $this->impactOnUnitPriceTaxIncluded = $impactOnUnitPriceTaxIncluded;
        $this->ecotax = $ecotax;
        $this->ecotaxTaxIncluded = $ecotaxTaxIncluded;
        $this->wholesalePrice = $wholesalePrice;
        $this->productTaxRate = $productTaxRate;
        $this->productPrice = $productPrice;
        $this->productEcotax = $productEcotax;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnPrice(): DecimalNumber
    {
        return $this->impactOnPrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnPriceTaxIncluded(): DecimalNumber
    {
        return $this->impactOnPriceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnUnitPrice(): DecimalNumber
    {
        return $this->impactOnUnitPrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnUnitPriceTaxIncluded(): DecimalNumber
    {
        return $this->impactOnUnitPriceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getEcotax(): DecimalNumber
    {
        return $this->ecotax;
    }

    /**
     * @return DecimalNumber
     */
    public function getEcotaxTaxIncluded(): DecimalNumber
    {
        return $this->ecotaxTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getWholesalePrice(): DecimalNumber
    {
        return $this->wholesalePrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductTaxRate(): DecimalNumber
    {
        return $this->productTaxRate;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductPrice(): DecimalNumber
    {
        return $this->productPrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductEcotax(): DecimalNumber
    {
        return $this->productEcotax;
    }
}
