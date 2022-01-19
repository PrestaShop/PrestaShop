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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class RangeStockMovementHistory implements StockMovementHistory
{
    /**
     * @var int[]
     */
    protected $stockMovementIds = [];

    /**
     * @var int[]
     */
    protected $stockIds = [];

    /**
     * @var int[]
     */
    protected $orderIds = [];

    /**
     * @var int[]
     */
    protected $stockMovementReasonIds = [];

    /**
     * @var int[]
     */
    protected $employeeIds = [];

    /**
     * @var string|null
     */
    protected $employeeName = null;

    /**
     * @var int
     */
    protected $deltaQuantity;

    /**
     * @var DateTimeInterface|null
     */
    protected $fromDate = null;

    /**
     * @var DateTimeInterface|null
     */
    protected $toDate = null;

    public function __construct(
        int $deltaQuantity,
        DateTimeInterface $fromDate,
        DateTimeInterface $toDate
    ) {
        $this->deltaQuantity = $deltaQuantity;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getStockMovementIds(): array
    {
        return $this->stockMovementIds;
    }

    public function setStockMovementIds(int ...$stockMovementIds): self
    {
        $this->stockMovementIds = $stockMovementIds;

        return $this;
    }

    public function getStockIds(): array
    {
        return $this->stockIds;
    }

    public function setStockIds(int ...$stockIds): self
    {
        $this->stockIds = $stockIds;

        return $this;
    }

    public function getStockMovementReasonIds(): array
    {
        return $this->stockMovementReasonIds;
    }

    public function setStockMovementReasonIds(int ...$stockMovementReasonIds): self
    {
        $this->stockMovementReasonIds = $stockMovementReasonIds;

        return $this;
    }

    public function getOrderIds(): array
    {
        return $this->orderIds;
    }

    public function setOrderIds(int ...$orderIds): self
    {
        $this->orderIds = $orderIds;

        return $this;
    }

    public function getEmployeeIds(): array
    {
        return $this->employeeIds;
    }

    public function setEmployeeIds(int ...$employeeIds): self
    {
        $this->employeeIds = $employeeIds;

        return $this;
    }

    public function getEmployeeName(?TranslatorInterface $translator = null): ?string
    {
        return $this->employeeName;
    }

    public function setEmployeeName(?string $employeeName): self
    {
        $this->employeeName = $employeeName;

        return $this;
    }

    public function getDeltaQuantity(): int
    {
        return $this->deltaQuantity;
    }

    public function getFromDate(): DateTimeInterface
    {
        return $this->fromDate;
    }

    public function getToDate(): DateTimeInterface
    {
        return $this->toDate;
    }

    public function getDates(): array
    {
        return [
            'from' => $this->getFromDate(),
            'to' => $this->getToDate(),
        ];
    }

    public function getDateRange(?TranslatorInterface $translator = null): string
    {
        $dateStrings = [];

        foreach ($this->getDates() as $key => $date) {
            $dateStrings[$key . 'Date'] = $date->format(DateTime::DEFAULT_DATETIME_FORMAT);
        }
        if ($translator instanceof TranslatorInterface) {
            // TODO Fix translation key & domain
            $dateRange = $translator->trans('%fromDate% - %toDate%', $dateStrings, 'domain???');
        } else {
            $dateRange = vsprintf('%s - %s', array_values($dateStrings));
        }

        return $dateRange;
    }
}
