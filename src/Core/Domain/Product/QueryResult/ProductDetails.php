<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds product details
 */
class ProductDetails
{
    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $upc;

    /**
     * @var string
     */
    private $gtin;

    /**
     * @var string
     */
    private $mpn;

    /**
     * @var string
     */
    private $reference;

    /**
     * @param string $isbn
     * @param string $upc
     * @param string $gtin
     * @param string $mpn
     * @param string $reference
     */
    public function __construct(
        string $isbn,
        string $upc,
        string $gtin,
        string $mpn,
        string $reference
    ) {
        $this->isbn = $isbn;
        $this->upc = $upc;
        $this->gtin = $gtin;
        $this->mpn = $mpn;
        $this->reference = $reference;
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
    public function getUpc(): string
    {
        return $this->upc;
    }

    /**
     * @return string
     */
    public function getEan13(): string
    {
        return $this->getGtin();
    }

    public function getGtin(): string
    {
        return $this->gtin;
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
}
