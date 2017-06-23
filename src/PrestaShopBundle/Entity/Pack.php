<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pack
 *
 * @ORM\Table(indexes={@ORM\Index(name="product_item", columns={"id_product_item", "id_product_attribute_item"})})
 * @ORM\Entity
 */
class Pack
{
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_pack", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductPack;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_item", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductItem;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute_item", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductAttributeItem;



    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Pack
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set idProductPack
     *
     * @param integer $idProductPack
     *
     * @return Pack
     */
    public function setIdProductPack($idProductPack)
    {
        $this->idProductPack = $idProductPack;

        return $this;
    }

    /**
     * Get idProductPack
     *
     * @return integer
     */
    public function getIdProductPack()
    {
        return $this->idProductPack;
    }

    /**
     * Set idProductItem
     *
     * @param integer $idProductItem
     *
     * @return Pack
     */
    public function setIdProductItem($idProductItem)
    {
        $this->idProductItem = $idProductItem;

        return $this;
    }

    /**
     * Get idProductItem
     *
     * @return integer
     */
    public function getIdProductItem()
    {
        return $this->idProductItem;
    }

    /**
     * Set idProductAttributeItem
     *
     * @param integer $idProductAttributeItem
     *
     * @return Pack
     */
    public function setIdProductAttributeItem($idProductAttributeItem)
    {
        $this->idProductAttributeItem = $idProductAttributeItem;

        return $this;
    }

    /**
     * Get idProductAttributeItem
     *
     * @return integer
     */
    public function getIdProductAttributeItem()
    {
        return $this->idProductAttributeItem;
    }
}
