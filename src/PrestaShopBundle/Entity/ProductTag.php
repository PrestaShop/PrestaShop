<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductTag
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_tag", columns={"id_tag"}), @ORM\Index(name="id_lang", columns={"id_lang", "id_tag"})})
 * @ORM\Entity
 */
class ProductTag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=false)
     */
    private $idLang;

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
     * @ORM\Column(name="id_tag", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idTag;



    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return ProductTag
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductTag
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
     * Set idTag
     *
     * @param integer $idTag
     *
     * @return ProductTag
     */
    public function setIdTag($idTag)
    {
        $this->idTag = $idTag;

        return $this;
    }

    /**
     * Get idTag
     *
     * @return integer
     */
    public function getIdTag()
    {
        return $this->idTag;
    }
}
