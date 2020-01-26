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

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Stores editable data for order state
 */
class EditableOrderState
{
    /**
     * @var OrderStateId
     */
    private $orderStateId;
    /**
     * @var array
     */
    private $localizedNames;
    /**
     * @var string
     */
    private $color;
    /**
     * @var bool
     */
    private $logable;
    /**
     * @var bool
     */
    private $invoiceOn;
    /**
     * @var bool
     */
    private $hiddenOn;
    /**
     * @var bool
     */
    private $sendEmailOn;
    /**
     * @var bool
     */
    private $pdfInvoiceOn;
    /**
     * @var bool
     */
    private $pdfDeliveryOn;
    /**
     * @var bool
     */
    private $shippedOn;
    /**
     * @var bool
     */
    private $paidOn;
    /**
     * @var bool
     */
    private $deliveryOn;
    /**
     * @var array
     */
    private $localizedTemplates;

    public function __construct(
        OrderStateId $orderStateId,
        array $name,
        string $color,
        bool $logable,
        bool $invoiceOn,
        bool $hiddenOn,
        bool $sendEmailOn,
        bool $pdfInvoiceOn,
        bool $pdfDeliveryOn,
        bool $shippedOn,
        bool $paidOn,
        bool $deliveryOn,
        array $localizedTemplates
    ) {
        $this->orderStateId = $orderStateId;
        $this->localizedNames = $name;
        $this->color = $color;
        $this->logable = $logable;
        $this->invoiceOn = $invoiceOn;
        $this->hiddenOn = $hiddenOn;
        $this->sendEmailOn = $sendEmailOn;
        $this->pdfInvoiceOn = $pdfInvoiceOn;
        $this->pdfDeliveryOn = $pdfDeliveryOn;
        $this->shippedOn = $shippedOn;
        $this->paidOn = $paidOn;
        $this->deliveryOn = $deliveryOn;
        $this->localizedTemplates = $localizedTemplates;
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return bool
     */
    public function isLogable()
    {
        return $this->logable;
    }

    /**
     * @return bool
     */
    public function isInvoiceOn()
    {
        return $this->invoiceOn;
    }

    /**
     * @return bool
     */
    public function isHiddenOn()
    {
        return $this->hiddenOn;
    }

    /**
     * @return bool
     */
    public function isSendEmailOn()
    {
        return $this->sendEmailOn;
    }

    /**
     * @return bool
     */
    public function isPdfInvoiceOn()
    {
        return $this->pdfInvoiceOn;
    }

    /**
     * @return bool
     */
    public function isPdfDeliveryOn()
    {
        return $this->pdfDeliveryOn;
    }

    /**
     * @return bool
     */
    public function isShippedOn()
    {
        return $this->shippedOn;
    }

    /**
     * @return bool
     */
    public function isPaidOn()
    {
        return $this->paidOn;
    }

    /**
     * @return bool
     */
    public function isDeliveryOn()
    {
        return $this->deliveryOn;
    }

    /**
     * @return array
     */
    public function getLocalizedTemplates()
    {
        return $this->localizedTemplates;
    }
}
