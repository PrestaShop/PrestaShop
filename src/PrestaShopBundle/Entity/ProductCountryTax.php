<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductCountryTax
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductCountryTax
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax", type="integer", nullable=false)
     */
    private $idTax;

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
     * @ORM\Column(name="id_country", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCountry;



    /**
     * Set idTax
     *
     * @param integer $idTax
     *
     * @return ProductCountryTax
     */
    public function setIdTax($idTax)
    {
        $this->idTax = $idTax;

        return $this;
    }

    /**
     * Get idTax
     *
     * @return integer
     */
    public function getIdTax()
    {
        return $this->idTax;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductCountryTax
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
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return ProductCountryTax
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;

        return $this;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }
}
