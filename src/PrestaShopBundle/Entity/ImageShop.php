<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageShop
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_shop", "cover"})}, indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class ImageShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cover", type="boolean", nullable=true)
     */
    private $cover;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idImage;

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
     * @return ImageShop
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
     * Set cover
     *
     * @param boolean $cover
     *
     * @return ImageShop
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return boolean
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set idImage
     *
     * @param integer $idImage
     *
     * @return ImageShop
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;

        return $this;
    }

    /**
     * Get idImage
     *
     * @return integer
     */
    public function getIdImage()
    {
        return $this->idImage;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ImageShop
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
