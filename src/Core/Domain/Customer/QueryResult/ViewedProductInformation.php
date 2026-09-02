<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class ViewedProductInformation holds information about product that customer has viewed.
 */
class ViewedProductInformation
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var string
     */
    private $productUrl;

    /**
     * @param int $productId
     * @param string $productName
     * @param string $productUrl
     */
    public function __construct($productId, $productName, $productUrl)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->productUrl = $productUrl;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }
}
