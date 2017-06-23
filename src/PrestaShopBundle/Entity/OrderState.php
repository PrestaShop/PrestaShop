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
 * OrderState
 *
 * @ORM\Table(indexes={@ORM\Index(name="module_name", columns={"module_name"})})
 * @ORM\Entity
 */
class OrderState
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="invoice", type="boolean", nullable=true)
     */
    private $invoice = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_email", type="boolean", nullable=false)
     */
    private $sendEmail = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="module_name", type="string", length=255, nullable=true)
     */
    private $moduleName;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var boolean
     *
     * @ORM\Column(name="unremovable", type="boolean", nullable=false)
     */
    private $unremovable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hidden", type="boolean", nullable=false)
     */
    private $hidden = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="logable", type="boolean", nullable=false)
     */
    private $logable = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="delivery", type="boolean", nullable=false)
     */
    private $delivery = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipped", type="boolean", nullable=false)
     */
    private $shipped = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="paid", type="boolean", nullable=false)
     */
    private $paid = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="pdf_invoice", type="boolean", nullable=false)
     */
    private $pdfInvoice = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="pdf_delivery", type="boolean", nullable=false)
     */
    private $pdfDelivery = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderState;



    /**
     * Set invoice
     *
     * @param boolean $invoice
     *
     * @return OrderState
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return boolean
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set sendEmail
     *
     * @param boolean $sendEmail
     *
     * @return OrderState
     */
    public function setSendEmail($sendEmail)
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    /**
     * Get sendEmail
     *
     * @return boolean
     */
    public function getSendEmail()
    {
        return $this->sendEmail;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     *
     * @return OrderState
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Get moduleName
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return OrderState
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
     * Set unremovable
     *
     * @param boolean $unremovable
     *
     * @return OrderState
     */
    public function setUnremovable($unremovable)
    {
        $this->unremovable = $unremovable;

        return $this;
    }

    /**
     * Get unremovable
     *
     * @return boolean
     */
    public function getUnremovable()
    {
        return $this->unremovable;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     *
     * @return OrderState
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set logable
     *
     * @param boolean $logable
     *
     * @return OrderState
     */
    public function setLogable($logable)
    {
        $this->logable = $logable;

        return $this;
    }

    /**
     * Get logable
     *
     * @return boolean
     */
    public function getLogable()
    {
        return $this->logable;
    }

    /**
     * Set delivery
     *
     * @param boolean $delivery
     *
     * @return OrderState
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return boolean
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set shipped
     *
     * @param boolean $shipped
     *
     * @return OrderState
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;

        return $this;
    }

    /**
     * Get shipped
     *
     * @return boolean
     */
    public function getShipped()
    {
        return $this->shipped;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     *
     * @return OrderState
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set pdfInvoice
     *
     * @param boolean $pdfInvoice
     *
     * @return OrderState
     */
    public function setPdfInvoice($pdfInvoice)
    {
        $this->pdfInvoice = $pdfInvoice;

        return $this;
    }

    /**
     * Get pdfInvoice
     *
     * @return boolean
     */
    public function getPdfInvoice()
    {
        return $this->pdfInvoice;
    }

    /**
     * Set pdfDelivery
     *
     * @param boolean $pdfDelivery
     *
     * @return OrderState
     */
    public function setPdfDelivery($pdfDelivery)
    {
        $this->pdfDelivery = $pdfDelivery;

        return $this;
    }

    /**
     * Get pdfDelivery
     *
     * @return boolean
     */
    public function getPdfDelivery()
    {
        return $this->pdfDelivery;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return OrderState
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
     * Get idOrderState
     *
     * @return integer
     */
    public function getIdOrderState()
    {
        return $this->idOrderState;
    }
}
