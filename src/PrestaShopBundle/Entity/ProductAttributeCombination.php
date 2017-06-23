<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttributeCombination
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_product_attribute", columns={"id_product_attribute"})})
 * @ORM\Entity
 */
class ProductAttributeCombination
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductAttribute;



    /**
     * Set idAttribute
     *
     * @param integer $idAttribute
     *
     * @return ProductAttributeCombination
     */
    public function setIdAttribute($idAttribute)
    {
        $this->idAttribute = $idAttribute;

        return $this;
    }

    /**
     * Get idAttribute
     *
     * @return integer
     */
    public function getIdAttribute()
    {
        return $this->idAttribute;
    }

    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return ProductAttributeCombination
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
}
