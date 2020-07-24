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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Adapter\Entity\Employee;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class LogsGridDataFactory gets data for languages grid.
 */
final class LogsGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineLogsDataFactory;

    /**
     * @param GridDataFactoryInterface $doctrineLogsDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $doctrineLogsDataFactory
    ) {
        $this->doctrineLogsDataFactory = $doctrineLogsDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $logsData = $this->doctrineLogsDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $logsData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $logsData->getRecordsTotal(),
            $logsData->getQuery()
        );
    }

    /**
     * @param array $logs
     *
     * @return array
     */
    private function applyModification(array $logs)
    {
        foreach ($logs as $i => $log) {
            $employee = new Employee($log['id_employee']);
            $logs[$i]['image'] = $employee->getImage();
        }

        return $logs;
    }
}
