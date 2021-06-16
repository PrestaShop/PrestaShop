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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Edits employee dashboard data.
 */
class UpdateEmployeeStatsRangesCommand
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @var string
     */
    private $dateFrom;

    /**
     * @var string
     */
    private $dateTo;

    /**
     * @var bool
     */
    private $compare;

    /**
     * @var string|null
     */
    private $compareFrom;

    /**
     * @var string|null
     */
    private $compareTo;

    /**
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $compare
     * @param string|null $compareFrom
     * @param string|null $compareTo
     *
     * @throws InvalidEmployeeIdException
     */
    public function __construct(
        int $employeeId,
        string $dateFrom,
        string $dateTo,
        bool $compare,
        ?string $compareFrom,
        ?string $compareTo
    ) {
        $this->employeeId = new EmployeeId($employeeId);
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->compare = $compare;
        $this->compareFrom = $compareFrom;
        $this->compareTo = $compareTo;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }

    /**
     * @return string
     */
    public function getDateFrom(): string
    {
        return $this->dateFrom;
    }

    /**
     * @return string
     */
    public function getDateTo(): string
    {
        return $this->dateTo;
    }

    /**
     * @return bool
     */
    public function isCompare(): bool
    {
        return $this->compare;
    }

    /**
     * @return string|null
     */
    public function getCompareFrom(): ?string
    {
        return $this->compareFrom;
    }

    /**
     * @return string|null
     */
    public function getCompareTo(): ?string
    {
        return $this->compareTo;
    }
}
