<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerProductsInformation holds information about products that customers has bought and viewed.
 */
class ProductsInformation
{
    /**
     * @var BoughtProductInformation[]
     */
    private $boughtProductsInformation;

    /**
     * @var ViewedProductInformation[]
     */
    private $viewedProductsInformation;

    /**
     * @param BoughtProductInformation[] $boughtProductsInformation
     * @param ViewedProductInformation[] $viewedProductsInformation
     */
    public function __construct(array $boughtProductsInformation, array $viewedProductsInformation)
    {
        $this->boughtProductsInformation = $boughtProductsInformation;
        $this->viewedProductsInformation = $viewedProductsInformation;
    }

    /**
     * @return BoughtProductInformation[]
     */
    public function getBoughtProductsInformation()
    {
        return $this->boughtProductsInformation;
    }

    /**
     * @return ViewedProductInformation[]
     */
    public function getViewedProductsInformation()
    {
        return $this->viewedProductsInformation;
    }
}
