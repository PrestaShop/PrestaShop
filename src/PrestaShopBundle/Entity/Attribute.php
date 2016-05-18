<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\AttributeRepository")
 */
class Attribute
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\AttributeGroup")
     * @ORM\JoinColumn(name="id_attribute_group", referencedColumnName="id_attribute_group", nullable=false)
     */
    private $attributeGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_attribute", referencedColumnName="id_attribute")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop")}
     * )
     */
    private $shops;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shops = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Attribute
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Attribute
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
     * Set attributeGroup
     *
     * @param \PrestaShopBundle\Entity\AttributeGroup $attributeGroup
     *
     * @return Attribute
     */
    public function setAttributeGroup(\PrestaShopBundle\Entity\AttributeGroup $attributeGroup)
    {
        $this->attributeGroup = $attributeGroup;

        return $this;
    }

    /**
     * Get attributeGroup
     *
     * @return \PrestaShopBundle\Entity\AttributeGroup
     */
    public function getAttributeGroup()
    {
        return $this->attributeGroup;
    }

    /**
     * Add shop
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     *
     * @return Attribute
     */
    public function addShop(\PrestaShopBundle\Entity\Shop $shop)
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * Remove shop
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     */
    public function removeShop(\PrestaShopBundle\Entity\Shop $shop)
    {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShops()
    {
        return $this->shops;
    }
}
