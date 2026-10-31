<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Transfers combination details
 */
class CombinationDetails
{
    /**
     * @var string
     */
    private $gtin;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $mpn;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $upc;

    /**
     * @var DecimalNumber
     */
    private $impactOnWeight;

    /**
     * @var DecimalNumber
     */
    private $impactOnShippingCost;

    /**
     * @param string $gtin this is the new renamed ean13
     * @param string $isbn
     * @param string $mpn
     * @param string $reference
     * @param string $upc
     * @param DecimalNumber $impactOnWeight
     * @param DecimalNumber|null $impactOnShippingCost
     */
    public function __construct(
        string $gtin,
        string $isbn,
        string $mpn,
        string $reference,
        string $upc,
        DecimalNumber $impactOnWeight,
        ?DecimalNumber $impactOnShippingCost = null
    ) {
        $this->gtin = $gtin;
        $this->isbn = $isbn;
        $this->mpn = $mpn;
        $this->reference = $reference;
        $this->upc = $upc;
        $this->impactOnWeight = $impactOnWeight;
        // Trailing and optional so that a caller written before the field keeps working; a
        // combination with no impact of its own is worth zero, not null.
        $this->impactOnShippingCost = $impactOnShippingCost ?? new DecimalNumber('0');
    }

    /**
     * @return string
     */
    public function getGtin(): string
    {
        return $this->gtin;
    }

    public function getEan13(): string
    {
        return $this->getGtin();
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getUpc(): string
    {
        return $this->upc;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnWeight(): DecimalNumber
    {
        return $this->impactOnWeight;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnShippingCost(): DecimalNumber
    {
        return $this->impactOnShippingCost;
    }
}
