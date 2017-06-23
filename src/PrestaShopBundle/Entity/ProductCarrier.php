<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductCarrier
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductCarrier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier_reference", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCarrierReference;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductCarrier
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
     * Set idCarrierReference
     *
     * @param integer $idCarrierReference
     *
     * @return ProductCarrier
     */
    public function setIdCarrierReference($idCarrierReference)
    {
        $this->idCarrierReference = $idCarrierReference;

        return $this;
    }

    /**
     * Get idCarrierReference
     *
     * @return integer
     */
    public function getIdCarrierReference()
    {
        return $this->idCarrierReference;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ProductCarrier
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }
}
