<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use SplFileInfo;

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
     * @var SplFileInfo|null
     */
    protected $icon;
    /**
     * @var string
     */
    private $color;
    /**
     * @var bool
     */
    private $loggable;
    /**
     * @var bool
     */
    private $invoice;
    /**
     * @var bool
     */
    private $hidden;
    /**
     * @var bool
     */
    private $sendEmail;
    /**
     * @var bool
     */
    private $pdfInvoice;
    /**
     * @var bool
     */
    private $pdfDelivery;
    /**
     * @var bool
     */
    private $shipped;
    /**
     * @var bool
     */
    private $paid;
    /**
     * @var bool
     */
    private $delivery;
    /**
     * @var array
     */
    private $localizedTemplates;
    /**
     * @var bool
     */
    private $isDeleted;
    private $hidden_employee;

    public function __construct(
        OrderStateId $orderStateId,
        array $name,
        ?SplFileInfo $icon,
        string $color,
        bool $loggable,
        bool $invoice,
        bool $hidden,
        bool $sendEmail,
        bool $pdfInvoice,
        bool $pdfDelivery,
        bool $shipped,
        bool $paid,
        bool $delivery,
        array $localizedTemplates,
        bool $isDeleted,
        bool $hidden_employee
    ) {
        $this->orderStateId = $orderStateId;
        $this->localizedNames = $name;
        $this->icon = $icon;
        $this->color = $color;
        $this->loggable = $loggable;
        $this->invoice = $invoice;
        $this->hidden = $hidden;
        $this->sendEmail = $sendEmail;
        $this->pdfInvoice = $pdfInvoice;
        $this->pdfDelivery = $pdfDelivery;
        $this->shipped = $shipped;
        $this->paid = $paid;
        $this->delivery = $delivery;
        $this->localizedTemplates = $localizedTemplates;
        $this->isDeleted = $isDeleted;
        $this->hidden_employee = $hidden_employee;
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
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return bool
     */
    public function isLoggable()
    {
        return $this->loggable;
    }

    /**
     * @return bool
     */
    public function isInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function isHiddenEmployee()
    {
        return $this->hidden_employee;
    }

    /**
     * @return bool
     */
    public function isSendEmailEnabled()
    {
        return $this->sendEmail;
    }

    /**
     * @return bool
     */
    public function isPdfInvoice()
    {
        return $this->pdfInvoice;
    }

    /**
     * @return bool
     */
    public function isPdfDelivery()
    {
        return $this->pdfDelivery;
    }

    /**
     * @return bool
     */
    public function isShipped()
    {
        return $this->shipped;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     * @return bool
     */
    public function isDelivery()
    {
        return $this->delivery;
    }

    /**
     * @return array
     */
    public function getLocalizedTemplates()
    {
        return $this->localizedTemplates;
    }

    /**
     * @return SplFileInfo|null
     */
    public function getIcon(): ?SplFileInfo
    {
        return $this->icon;
    }
}
