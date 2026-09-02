<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult;

/**
 * Stores query result for getting manufacturer for viewing
 */
class ViewableManufacturer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $manufacturerAddresses;

    /**
     * @var array
     */
    private $manufacturerProducts;

    /**
     * @param string $name
     * @param array $manufacturerAddresses
     * @param array $manufacturerProducts
     */
    public function __construct($name, array $manufacturerAddresses, array $manufacturerProducts)
    {
        $this->name = $name;
        $this->manufacturerAddresses = $manufacturerAddresses;
        $this->manufacturerProducts = $manufacturerProducts;
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
    public function getManufacturerAddresses()
    {
        return $this->manufacturerAddresses;
    }

    /**
     * @return array
     */
    public function getManufacturerProducts()
    {
        return $this->manufacturerProducts;
    }
}
