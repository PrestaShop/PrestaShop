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
 * SupplyOrderState
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SupplyOrderState
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="delivery_note", type="boolean", nullable=false)
     */
    private $deliveryNote = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="editable", type="boolean", nullable=false)
     */
    private $editable = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="receipt_state", type="boolean", nullable=false)
     */
    private $receiptState = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="pending_receipt", type="boolean", nullable=false)
     */
    private $pendingReceipt = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="enclosed", type="boolean", nullable=false)
     */
    private $enclosed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSupplyOrderState;



    /**
     * Set deliveryNote
     *
     * @param boolean $deliveryNote
     *
     * @return SupplyOrderState
     */
    public function setDeliveryNote($deliveryNote)
    {
        $this->deliveryNote = $deliveryNote;

        return $this;
    }

    /**
     * Get deliveryNote
     *
     * @return boolean
     */
    public function getDeliveryNote()
    {
        return $this->deliveryNote;
    }

    /**
     * Set editable
     *
     * @param boolean $editable
     *
     * @return SupplyOrderState
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * Get editable
     *
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * Set receiptState
     *
     * @param boolean $receiptState
     *
     * @return SupplyOrderState
     */
    public function setReceiptState($receiptState)
    {
        $this->receiptState = $receiptState;

        return $this;
    }

    /**
     * Get receiptState
     *
     * @return boolean
     */
    public function getReceiptState()
    {
        return $this->receiptState;
    }

    /**
     * Set pendingReceipt
     *
     * @param boolean $pendingReceipt
     *
     * @return SupplyOrderState
     */
    public function setPendingReceipt($pendingReceipt)
    {
        $this->pendingReceipt = $pendingReceipt;

        return $this;
    }

    /**
     * Get pendingReceipt
     *
     * @return boolean
     */
    public function getPendingReceipt()
    {
        return $this->pendingReceipt;
    }

    /**
     * Set enclosed
     *
     * @param boolean $enclosed
     *
     * @return SupplyOrderState
     */
    public function setEnclosed($enclosed)
    {
        $this->enclosed = $enclosed;

        return $this;
    }

    /**
     * Get enclosed
     *
     * @return boolean
     */
    public function getEnclosed()
    {
        return $this->enclosed;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return SupplyOrderState
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
     * Get idSupplyOrderState
     *
     * @return integer
     */
    public function getIdSupplyOrderState()
    {
        return $this->idSupplyOrderState;
    }
}
