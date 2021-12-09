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

class OrderReturnForViewing
{
    /**
     * @var int
     */
    private $orderInvoiceId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $stateName;

    /**
     * @var string
     */
    private $orderReturnNumber;

    /**
     * @var int
     */
    private $idOrderReturn;

    /**
     * @param int $idOrderReturn
     * @param int $orderInvoiceId
     * @param DateTimeImmutable $date
     * @param string $type
     * @param string $stateName
     * @param string $orderReturnNumber
     */
    public function __construct(
        int $idOrderReturn,
        int $orderInvoiceId,
        DateTimeImmutable $date,
        string $type,
        string $stateName,
        string $orderReturnNumber
    ) {
        $this->orderInvoiceId = $orderInvoiceId;
        $this->date = $date;
        $this->type = $type;
        $this->stateName = $stateName;
        $this->idOrderReturn = $idOrderReturn;
        $this->orderReturnNumber = $orderReturnNumber;
    }

    /**
     * @return int
     */
    public function getOrderInvoiceId(): int
    {
        return $this->orderInvoiceId;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStateName(): string
    {
        return $this->stateName;
    }

    /**
     * @return string
     */
    public function getOrderReturnNumber(): string
    {
        return $this->orderReturnNumber;
    }

    /**
     * @return int
     */
    public function getIdOrderReturn(): int
    {
        return $this->idOrderReturn;
    }
}
