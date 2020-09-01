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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderCarrierForViewing
{
    /**
     * @var int
     */
    private $orderCarrierId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $carrierId;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string|null
     */
    private $trackingUrl;

    /**
     * @var string|null
     */
    private $trackingNumber;

    /**
     * @var bool
     */
    private $canEdit;

    /**
     * @var string
     */
    private $weight;

    /**
     * @param int $orderCarrierId
     * @param DateTimeImmutable $date
     * @param string $name Carrier name or null in case of virtual order
     * @param string $weight
     * @param int $carrierId
     * @param string $price Price or null in case of virtual order
     * @param string|null $trackingUrl
     * @param string|null $trackingNumber
     * @param bool $canEdit
     */
    public function __construct(
        int $orderCarrierId,
        DateTimeImmutable $date,
        ?string $name,
        string $weight,
        int $carrierId,
        ?string $price,
        ?string $trackingUrl,
        ?string $trackingNumber,
        bool $canEdit
    ) {
        $this->orderCarrierId = $orderCarrierId;
        $this->date = $date;
        $this->name = $name;
        $this->carrierId = $carrierId;
        $this->price = $price;
        $this->trackingUrl = $trackingUrl;
        $this->trackingNumber = $trackingNumber;
        $this->canEdit = $canEdit;
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getOrderCarrierId(): int
    {
        return $this->orderCarrierId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    /**
     * @return string
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @return string|null
     */
    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }

    /**
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        return $this->weight;
    }
}
