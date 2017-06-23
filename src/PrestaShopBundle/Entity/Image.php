<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product_cover", columns={"id_product", "cover"}), @ORM\UniqueConstraint(name="idx_product_image", columns={"id_image", "id_product", "cover"})}, indexes={@ORM\Index(name="image_product", columns={"id_product"})})
 * @ORM\Entity
 */
class Image
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
     * @ORM\Column(name="position", type="smallint", nullable=false)
     */
    private $position = '0';

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
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idImage;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return Image
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
     * Set position
     *
     * @param integer $position
     *
     * @return Image
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set cover
     *
     * @param boolean $cover
     *
     * @return Image
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
     * Get idImage
     *
     * @return integer
     */
    public function getIdImage()
    {
        return $this->idImage;
    }
}
