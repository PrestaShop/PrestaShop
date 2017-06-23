<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseProductLocation
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_product_attribute", "id_warehouse"})})
 * @ORM\Entity
 */
class WarehouseProductLocation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer", nullable=false)
     */
    private $idProductAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer", nullable=false)
     */
    private $idWarehouse;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=64, nullable=true)
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse_product_location", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWarehouseProductLocation;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return WarehouseProductLocation
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return integer
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return WarehouseProductLocation
     */
    public function setIdProductAttribute($idProductAttribute)
    {
        $this->idProductAttribute = $idProductAttribute;

        return $this;
    }

    /**
     * Get idProductAttribute
     *
     * @return integer
     */
    public function getIdProductAttribute()
    {
        return $this->idProductAttribute;
    }

    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return WarehouseProductLocation
     */
    public function setIdWarehouse($idWarehouse)
    {
        $this->idWarehouse = $idWarehouse;

        return $this;
    }

    /**
     * Get idWarehouse
     *
     * @return integer
     */
    public function getIdWarehouse()
    {
        return $this->idWarehouse;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return WarehouseProductLocation
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get idWarehouseProductLocation
     *
     * @return integer
     */
    public function getIdWarehouseProductLocation()
    {
        return $this->idWarehouseProductLocation;
    }
}
