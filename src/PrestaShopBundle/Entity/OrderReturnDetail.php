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
 * OrderReturnDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderReturnDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_return", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderReturn;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderDetail;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization", type="integer", options={"default":0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCustomization;



    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     *
     * @return OrderReturnDetail
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return integer
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set idOrderReturn
     *
     * @param integer $idOrderReturn
     *
     * @return OrderReturnDetail
     */
    public function setIdOrderReturn($idOrderReturn)
    {
        $this->idOrderReturn = $idOrderReturn;

        return $this;
    }

    /**
     * Get idOrderReturn
     *
     * @return integer
     */
    public function getIdOrderReturn()
    {
        return $this->idOrderReturn;
    }

    /**
     * Set idOrderDetail
     *
     * @param integer $idOrderDetail
     *
     * @return OrderReturnDetail
     */
    public function setIdOrderDetail($idOrderDetail)
    {
        $this->idOrderDetail = $idOrderDetail;

        return $this;
    }

    /**
     * Get idOrderDetail
     *
     * @return integer
     */
    public function getIdOrderDetail()
    {
        return $this->idOrderDetail;
    }

    /**
     * Set idCustomization
     *
     * @param integer $idCustomization
     *
     * @return OrderReturnDetail
     */
    public function setIdCustomization($idCustomization)
    {
        $this->idCustomization = $idCustomization;

        return $this;
    }

    /**
     * Get idCustomization
     *
     * @return integer
     */
    public function getIdCustomization()
    {
        return $this->idCustomization;
    }
}
