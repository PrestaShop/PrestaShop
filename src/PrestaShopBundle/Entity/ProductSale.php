<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSale
 *
 * @ORM\Table(indexes={@ORM\Index(name="quantity", columns={"quantity"})})
 * @ORM\Entity
 */
class ProductSale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="sale_nbr", type="integer", nullable=false)
     */
    private $saleNbr = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="date", nullable=true)
     */
    private $dateUpd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProduct;



    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ProductSale
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set saleNbr
     *
     * @param integer $saleNbr
     *
     * @return ProductSale
     */
    public function setSaleNbr($saleNbr)
    {
        $this->saleNbr = $saleNbr;

        return $this;
    }

    /**
     * Get saleNbr
     *
     * @return integer
     */
    public function getSaleNbr()
    {
        return $this->saleNbr;
    }

    /**
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return ProductSale
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
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
}
