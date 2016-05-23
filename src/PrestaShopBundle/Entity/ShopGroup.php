<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\ShopGroupRepository")
 */
class ShopGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="share_customer", type="boolean")
     */
    private $shareCustomer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="share_order", type="boolean")
     */
    private $shareOrder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="share_stock", type="boolean")
     */
    private $shareStock;

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
     * @return ShopGroup
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
     * Set shareCustomer
     *
     * @param boolean $shareCustomer
     *
     * @return ShopGroup
     */
    public function setShareCustomer($shareCustomer)
    {
        $this->shareCustomer = $shareCustomer;

        return $this;
    }

    /**
     * Get shareCustomer
     *
     * @return boolean
     */
    public function getShareCustomer()
    {
        return $this->shareCustomer;
    }

    /**
     * Set shareOrder
     *
     * @param boolean $shareOrder
     *
     * @return ShopGroup
     */
    public function setShareOrder($shareOrder)
    {
        $this->shareOrder = $shareOrder;

        return $this;
    }

    /**
     * Get shareOrder
     *
     * @return boolean
     */
    public function getShareOrder()
    {
        return $this->shareOrder;
    }

    /**
     * Set shareStock
     *
     * @param boolean $shareStock
     *
     * @return ShopGroup
     */
    public function setShareStock($shareStock)
    {
        $this->shareStock = $shareStock;

        return $this;
    }

    /**
     * Get shareStock
     *
     * @return boolean
     */
    public function getShareStock()
    {
        return $this->shareStock;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ShopGroup
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
     * @return ShopGroup
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
}
