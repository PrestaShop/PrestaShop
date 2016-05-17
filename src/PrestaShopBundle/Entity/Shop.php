<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\ShopRepository")
 */
class Shop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\ShopGroup")
     * @ORM\JoinColumn(name="id_shop_group", referencedColumnName="id_shop_group", nullable=false)
     */
    private $shopGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_category", type="integer")
     */
    private $idCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="theme_name", type="string", length=255)
     */
    private $themeName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;


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
     * Set name
     *
     * @param string $name
     *
     * @return Shop
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set idCategory
     *
     * @param integer $idCategory
     *
     * @return Shop
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * Get idCategory
     *
     * @return integer
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * Set themeName
     *
     * @param string $themeName
     *
     * @return Shop
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

        return $this;
    }

    /**
     * Get themeName
     *
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Shop
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Shop
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set shopGroup
     *
     * @param \PrestaShopBundle\Entity\ShopGroup $shopGroup
     *
     * @return Shop
     */
    public function setShopGroup(\PrestaShopBundle\Entity\ShopGroup $shopGroup)
    {
        $this->shopGroup = $shopGroup;

        return $this;
    }

    /**
     * Get shopGroup
     *
     * @return \PrestaShopBundle\Entity\ShopGroup
     */
    public function getShopGroup()
    {
        return $this->shopGroup;
    }
}
