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
 * OrderInvoicePayment
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_payment", columns={"id_order_payment"}), @ORM\Index(name="id_order", columns={"id_order"})})
 * @ORM\Entity
 */
class OrderInvoicePayment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=false)
     */
    private $idOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_payment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderPayment;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderInvoicePayment
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return integer
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idOrderInvoice
     *
     * @param integer $idOrderInvoice
     *
     * @return OrderInvoicePayment
     */
    public function setIdOrderInvoice($idOrderInvoice)
    {
        $this->idOrderInvoice = $idOrderInvoice;

        return $this;
    }

    /**
     * Get idOrderInvoice
     *
     * @return integer
     */
    public function getIdOrderInvoice()
    {
        return $this->idOrderInvoice;
    }

    /**
     * Set idOrderPayment
     *
     * @param integer $idOrderPayment
     *
     * @return OrderInvoicePayment
     */
    public function setIdOrderPayment($idOrderPayment)
    {
        $this->idOrderPayment = $idOrderPayment;

        return $this;
    }

    /**
     * Get idOrderPayment
     *
     * @return integer
     */
    public function getIdOrderPayment()
    {
        return $this->idOrderPayment;
    }
}
