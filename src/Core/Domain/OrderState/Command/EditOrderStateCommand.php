<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\Name;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Edits provided order state.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing order state.
 * For example, if the name is null, then the original value is not modified,
 * however, if name is set, then the original value will be overwritten.
 */
class EditOrderStateCommand
{
    /**
     * @var OrderStateId
     */
    private $orderStateId;

    /**
     * @var Name|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $color;

    /**
     * @var bool|null
     */
    private $logable;

    /**
     * @var bool|null
     */
    private $invoiceOn;

    /**
     * @var bool|null
     */
    private $hiddenOn;

    /**
     * @var bool|null
     */
    private $sendEmailOn;

    /**
     * @var bool|null
     */
    private $pdfInvoiceOn;

    /**
     * @var bool|null
     */
    private $pdfDeliveryOn;

    /**
     * @var bool|null
     */
    private $shippedOn;

    /**
     * @var bool|null
     */
    private $paidOn;

    /**
     * @var bool|null
     */
    private $deliveryOn;

    /**
     * @var array|null
     */
    private $template;

    /**
     * @param int $orderStateId
     */
    public function __construct($orderStateId)
    {
        $this->orderStateId = new OrderStateId($orderStateId);
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }

    /**
     * @return Name|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return self
     */
    public function setColor(?string $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isLogable()
    {
        return $this->logable;
    }

    /**
     * @return self
     */
    public function setLogable(?bool $logable)
    {
        $this->logable = $logable;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isInvoiceOn()
    {
        return $this->invoiceOn;
    }

    /**
     * @return self
     */
    public function setInvoiceOn(?bool $invoiceOn)
    {
        $this->invoiceOn = $invoiceOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isHiddenOn()
    {
        return $this->hiddenOn;
    }

    /**
     * @return self
     */
    public function setHiddenOn(?bool $hiddenOn)
    {
        $this->hiddenOn = $hiddenOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isSendEmailOn()
    {
        return $this->sendEmailOn;
    }

    /**
     * @return self
     */
    public function setSendEmailOn(?bool $sendEmailOn)
    {
        $this->sendEmailOn = $sendEmailOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPdfInvoiceOn()
    {
        return $this->pdfInvoiceOn;
    }

    /**
     * @return self
     */
    public function setPdfInvoiceOn(?bool $pdfInvoiceOn)
    {
        $this->pdfInvoiceOn = $pdfInvoiceOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPdfDeliveryOn()
    {
        return $this->pdfDeliveryOn;
    }

    /**
     * @return self
     */
    public function setPdfDeliveryOn(?bool $pdfDeliveryOn)
    {
        $this->pdfDeliveryOn = $pdfDeliveryOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isShippedOn()
    {
        return $this->shippedOn;
    }

    /**
     * @return self
     */
    public function setShippedOn(?bool $shippedOn)
    {
        $this->shippedOn = $shippedOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPaidOn()
    {
        return $this->paidOn;
    }

    /**
     * @return self
     */
    public function setPaidOn(?bool $paidOn)
    {
        $this->paidOn = $paidOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isDeliveryOn()
    {
        return $this->deliveryOn;
    }

    /**
     * @return self
     */
    public function setDeliveryOn(?bool $deliveryOn)
    {
        $this->deliveryOn = $deliveryOn;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return self
     */
    public function setTemplate(?array $template)
    {
        $this->template = $template;

        return $this;
    }
}
