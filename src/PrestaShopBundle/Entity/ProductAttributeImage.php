<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttributeImage
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_image", columns={"id_image"})})
 * @ORM\Entity
 */
class ProductAttributeImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idImage;



    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return ProductAttributeImage
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
     * Set idImage
     *
     * @param integer $idImage
     *
     * @return ProductAttributeImage
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
}
