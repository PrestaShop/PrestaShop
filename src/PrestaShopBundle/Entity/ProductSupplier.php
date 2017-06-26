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
 * ProductSupplier
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_product_attribute", "id_supplier"})}, indexes={@ORM\Index(name="id_supplier", columns={"id_supplier", "id_product"})})
 * @ORM\Entity
 */
class ProductSupplier
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
     * @ORM\Column(name="id_product_attribute", type="integer", nullable=false, options={"default":0})
     */
    private $idProductAttribute = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer", nullable=false)
     */
    private $idSupplier;

    /**
     * @var string
     *
     * @ORM\Column(name="product_supplier_reference", type="string", length=32, nullable=true)
     */
    private $productSupplierReference;

    /**
     * @var string
     *
     * @ORM\Column(name="product_supplier_price_te", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $productSupplierPriceTe = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_supplier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductSupplier;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductSupplier
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
     * @return ProductSupplier
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
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return ProductSupplier
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return integer
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set productSupplierReference
     *
     * @param string $productSupplierReference
     *
     * @return ProductSupplier
     */
    public function setProductSupplierReference($productSupplierReference)
    {
        $this->productSupplierReference = $productSupplierReference;

        return $this;
    }

    /**
     * Get productSupplierReference
     *
     * @return string
     */
    public function getProductSupplierReference()
    {
        return $this->productSupplierReference;
    }

    /**
     * Set productSupplierPriceTe
     *
     * @param string $productSupplierPriceTe
     *
     * @return ProductSupplier
     */
    public function setProductSupplierPriceTe($productSupplierPriceTe)
    {
        $this->productSupplierPriceTe = $productSupplierPriceTe;

        return $this;
    }

    /**
     * Get productSupplierPriceTe
     *
     * @return string
     */
    public function getProductSupplierPriceTe()
    {
        return $this->productSupplierPriceTe;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return ProductSupplier
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * Get idCurrency
     *
     * @return integer
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * Get idProductSupplier
     *
     * @return integer
     */
    public function getIdProductSupplier()
    {
        return $this->idProductSupplier;
    }
}
