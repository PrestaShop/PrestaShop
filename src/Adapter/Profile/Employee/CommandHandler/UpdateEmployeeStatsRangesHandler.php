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

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Profile\Employee\AbstractEmployeeHandler;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\UpdateEmployeeStatsRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\UpdateEmployeeStatsRangesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotUpdateEmployeeStatsRanges;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShopException;

/**
 * Handles command that updates employee dashboard data.
 */
class UpdateEmployeeStatsRangesHandler extends AbstractEmployeeHandler implements UpdateEmployeeStatsRangesHandlerInterface
{
    private const DEFAULT_COMPARE_OPTION = 1;

    /**
     * @var Validate
     */
    private $validator;

    /**
     * @param Validate $validator
     */
    public function __construct(Validate $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateEmployeeStatsRangesCommand $command): void
    {
        $invalid = false;

        if (!$this->validator::isDate($command->getDateFrom()) && !$invalid) {
            $invalid = true;
        }

        if (!$this->validator::isDate($command->getDateTo()) && !$invalid) {
            $invalid = true;
        }

        if ($command->isCompare()) {
            if (!$this->validator::isDate($command->getCompareFrom()) && !$invalid) {
                $invalid = true;
            }

            if (!$this->validator::isDate($command->getCompareTo()) && !$invalid) {
                $invalid = true;
            }
        }

        if ($invalid) {
            throw new CannotUpdateEmployeeStatsRanges('Invalid dashboard data given.', CannotUpdateEmployeeStatsRanges::INVALID_DATE);
        }

        $employee = $this->getEmployee($command->getEmployeeId());

        try {
            $employee->stats_date_from = $command->getDateFrom();
            $employee->stats_date_to = $command->getDateTo();

            if ($command->isCompare()) {
                $employee->stats_compare_option = $command->isCompare();
                $employee->stats_compare_from = $command->getCompareFrom();
                $employee->stats_compare_to = $command->getCompareTo();
            } else {
                $employee->stats_compare_option = self::DEFAULT_COMPARE_OPTION;
                $employee->stats_compare_from = null;
                $employee->stats_compare_to = null;
            }

            if (!$employee->update()) {
                throw new EmployeeException(sprintf('Cannot update employee with id "%s"', $employee->id));
            }
        } catch (PrestaShopException $e) {
            throw new EmployeeException(sprintf('Cannot update employee with id "%s"', $employee->id));
        }
    }
}
