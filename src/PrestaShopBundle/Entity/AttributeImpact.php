<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeImpact
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_attribute"})})
 * @ORM\Entity
 */
class AttributeImpact
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
     * @ORM\Column(name="id_attribute", type="integer", nullable=false)
     */
    private $idAttribute;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=17, scale=2, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_attribute_impact", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAttributeImpact;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return AttributeImpact
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
     * Set idAttribute
     *
     * @param integer $idAttribute
     *
     * @return AttributeImpact
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
     * Set weight
     *
     * @param string $weight
     *
     * @return AttributeImpact
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return AttributeImpact
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
     * Get idAttributeImpact
     *
     * @return integer
     */
    public function getIdAttributeImpact()
    {
        return $this->idAttributeImpact;
    }
}
