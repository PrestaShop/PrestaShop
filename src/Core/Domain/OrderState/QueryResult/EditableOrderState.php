<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        bool $isDeleted
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
