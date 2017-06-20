<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Attribute
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="attribute_group", columns={"id_attribute_group"})}
 * )
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeRepository")
 */

class Attribute
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id_attribute", type="integer")
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
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\AttributeLang", mappedBy="attribute")
     */
    private $attributeLangs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->attributeLangs = new ArrayCollection();
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

    public function addAttributeLang(AttributeLang $attributeLang)
    {
        $this->attributeLangs[] = $attributeLang;

        $attributeLang->setAttribute($this);

        return $this;
    }

    public function removeAttributeLang(AttributeLang $attributeLang)
    {
        $this->attributeLangs->removeElement($attributeLang);
    }

    public function getAttributeLangs()
    {
        return $this->attributeLangs;
    }
}
