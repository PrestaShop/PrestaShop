<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\SqlManager\Exporter;

use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResultQuery;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Export\ExportDirectory;
use PrestaShop\PrestaShop\Core\SqlManager\Exception\SqlManagerExportException;
use SplFileObject;

/**
 * Class SqlRequestExporter is responsible for exporting SqlRequest
 */
final class SqlRequestExporter implements SqlRequestExporterInterface
{
    /**
     * @var ExportDirectory
     */
    private $exportDirectory;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param ExportDirectory $exportDirectory
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        ExportDirectory $exportDirectory,
        CommandBusInterface $queryBus
    ) {
        $this->exportDirectory = $exportDirectory;
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function exportToFile($sqlRequestId)
    {
        $sqlRequestExecutionResult = $this->getSqlRequestExecutionResult($sqlRequestId);
        $exportFile = $this->exportSqlRequestExecutionResult($sqlRequestId, $sqlRequestExecutionResult);

        return $exportFile;
    }

    /**
     * @param int $sqlRequestId
     *
     * @return SqlRequestExecutionResult
     */
    private function getSqlRequestExecutionResult($sqlRequestId)
    {
        try {
            $query = new GetSqlRequestExecutionResultQuery($sqlRequestId);

            /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
            $sqlRequestExecutionResult = $this->queryBus->handle($query);
        } catch (SqlRequestException $e) {
            throw new SqlManagerExportException(
                'Cannot retrieve SqlRequest data',
                SqlManagerExportException::SQL_REQUEST_ERROR
            );
        }

        if (empty($sqlRequestExecutionResult->getRows())) {
            throw new SqlManagerExportException(
                sprintf('SqlRequest with id "%s" did not return any data to export', $sqlRequestId),
                SqlManagerExportException::SQL_REQUEST_HAS_NO_DATA
            );
        }

        return $sqlRequestExecutionResult;
    }

    /**
     * @param int $sqlRequestId
     *
     * @return SplFileObject
     */
    private function createExportFile($sqlRequestId)
    {
        $fileName = sprintf('request_sql_%s.csv', $sqlRequestId);
        $filePath = $this->exportDirectory.$fileName;

        try {
            $exportFile = new SplFileObject($filePath, 'w');
        } catch (Exception $e) {
            throw new SqlManagerExportException(
                sprintf('Failed to create "%s" export file', $filePath),
                SqlManagerExportException::FAILED_TO_CREATE_EXPORT_FILE
            );
        }

        return $exportFile;
    }

    /**
     * @param int $sqlRequestId
     * @param SqlRequestExecutionResult $result
     *
     * @return SplFileObject
     */
    private function exportSqlRequestExecutionResult(
        $sqlRequestId,
        SqlRequestExecutionResult $result
    ) {
        $exportFile = $this->createExportFile($sqlRequestId);
        $exportFile->fputcsv($result->getColumns(), ';');

        foreach ($result->getRows() as $row) {
            $exportFile->fputcsv($row, ';');
        }

        return $exportFile;
    }
}
