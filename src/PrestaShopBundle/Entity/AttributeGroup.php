<?php

namespace PrestaShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\AttributeGroupRepository")
 */
class AttributeGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_attribute_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_color_group", type="boolean")
     */
    private $isColorGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="group_type", type="string", length=255)
     */
    private $groupType;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_attribute_group", referencedColumnName="id_attribute_group")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop")}
     * )
     */
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\AttributeGroupLang", mappedBy="attributeGroup", orphanRemoval=true)
     */
    private $attributeGroupLangs;

    private $groupTypeAvailable = array(
        'select',
        'radio',
        'color',
    );

    public function __construct()
    {
        $this->groupType = 'select';
        $this->shops = new ArrayCollection();
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
     * Set isColorGroup
     *
     * @param boolean $isColorGroup
     *
     * @return AttributeGroup
     */
    public function setIsColorGroup($isColorGroup)
    {
        $this->isColorGroup = $isColorGroup;

        return $this;
    }

    /**
     * Get isColorGroup
     *
     * @return boolean
     */
    public function getIsColorGroup()
    {
        return $this->isColorGroup;
    }

    /**
     * Set groupType
     *
     * @param string $groupType
     *
     * @return AttributeGroup
     */
    public function setGroupType($groupType)
    {
        if (!in_array($groupType, $this->groupTypeAvailable)) {
            throw new \InvalidArgumentException("Invalid group type");
        }

        $this->groupType = $groupType;

        return $this;
    }

    /**
     * Get groupType
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return AttributeGroup
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
     * Add shop
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     *
     * @return AttributeGroup
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

    public function addAttributeGroupLang(AttributeGroupLang $attributeGroupLang)
    {
        $this->attributeGroupLangs[] = $attributeGroupLang;

        $attributeGroupLang->setAttributeGroup($this);

        return $this;
    }

    public function removeAttributeGroupLang(AttributeGroupLang $attributeGroupLang)
    {
        $this->attributeGroupLangs->removeElement($attributeGroupLang);
    }

    public function getAttributeGroupLangs()
    {
        return $this->attributeGroupLangs;
    }
}
