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
