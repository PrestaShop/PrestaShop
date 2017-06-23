<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Delivery
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_zone", columns={"id_zone"}), @ORM\Index(name="id_carrier", columns={"id_carrier", "id_zone"}), @ORM\Index(name="id_range_price", columns={"id_range_price"}), @ORM\Index(name="id_range_weight", columns={"id_range_weight"})})
 * @ORM\Entity
 */
class Delivery
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=true)
     */
    private $idShop;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=true)
     */
    private $idShopGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer", nullable=false)
     */
    private $idCarrier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_range_price", type="integer", nullable=true)
     */
    private $idRangePrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_range_weight", type="integer", nullable=true)
     */
    private $idRangeWeight;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_zone", type="integer", nullable=false)
     */
    private $idZone;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_delivery", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idDelivery;



    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return Delivery
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

    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return Delivery
     */
    public function setIdShopGroup($idShopGroup)
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * Get idShopGroup
     *
     * @return integer
     */
    public function getIdShopGroup()
    {
        return $this->idShopGroup;
    }

    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return Delivery
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }

    /**
     * Set idRangePrice
     *
     * @param integer $idRangePrice
     *
     * @return Delivery
     */
    public function setIdRangePrice($idRangePrice)
    {
        $this->idRangePrice = $idRangePrice;

        return $this;
    }

    /**
     * Get idRangePrice
     *
     * @return integer
     */
    public function getIdRangePrice()
    {
        return $this->idRangePrice;
    }

    /**
     * Set idRangeWeight
     *
     * @param integer $idRangeWeight
     *
     * @return Delivery
     */
    public function setIdRangeWeight($idRangeWeight)
    {
        $this->idRangeWeight = $idRangeWeight;

        return $this;
    }

    /**
     * Get idRangeWeight
     *
     * @return integer
     */
    public function getIdRangeWeight()
    {
        return $this->idRangeWeight;
    }

    /**
     * Set idZone
     *
     * @param integer $idZone
     *
     * @return Delivery
     */
    public function setIdZone($idZone)
    {
        $this->idZone = $idZone;

        return $this;
    }

    /**
     * Get idZone
     *
     * @return integer
     */
    public function getIdZone()
    {
        return $this->idZone;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Delivery
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get idDelivery
     *
     * @return integer
     */
    public function getIdDelivery()
    {
        return $this->idDelivery;
    }
}
