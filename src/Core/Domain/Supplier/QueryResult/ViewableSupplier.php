<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult;

/**
 * Stores query result for getting supplier for viewing
 */
class ViewableSupplier
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $supplierProducts;

    /**
     * @param string $name
     * @param array $supplierProducts
     */
    public function __construct($name, array $supplierProducts)
    {
        $this->name = $name;
        $this->supplierProducts = $supplierProducts;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getSupplierProducts()
    {
        return $this->supplierProducts;
    }
}
