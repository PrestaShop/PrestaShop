<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Import;

use PrestaShop\PrestaShop\Core\Configuration\IniConfiguration;
use PrestaShop\PrestaShop\Core\Import\Access\ImportAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\InvalidDataRowException;
use PrestaShop\PrestaShop\Core\Import\Exception\SkippedIterationException;
use PrestaShop\PrestaShop\Core\Import\File\FileReaderInterface;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerInterface;
use SplFileInfo;

/**
 * Class Importer is responsible for data import.
 */
final class Importer implements ImporterInterface
{
    /**
     * @var ImportEntityDeleterInterface
     */
    private $entityDeleter;

    /**
     * @var ImportAccessCheckerInterface
     */
    private $accessChecker;

    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @var ImportDirectory
     */
    private $importDir;

    /**
     * @var IniConfiguration
     */
    private $iniConfiguration;

    /**
     * @param ImportAccessCheckerInterface $accessChecker
     * @param ImportEntityDeleterInterface $entityDeleter
     * @param FileReaderInterface $fileReader
     * @param ImportDirectory $importDir
     * @param IniConfiguration $iniConfiguration
     */
    public function __construct(
        ImportAccessCheckerInterface $accessChecker,
        ImportEntityDeleterInterface $entityDeleter,
        FileReaderInterface $fileReader,
        ImportDirectory $importDir,
        IniConfiguration $iniConfiguration
    ) {
        $this->entityDeleter = $entityDeleter;
        $this->accessChecker = $accessChecker;
        $this->fileReader = $fileReader;
        $this->importDir = $importDir;
        $this->iniConfiguration = $iniConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function import(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        ImportHandlerInterface $importHandler
    ) {
        $this->setUp($importHandler, $importConfig, $runtimeConfig);

        $importFile = new SplFileInfo($this->importDir . $importConfig->getFileName());

        // Current row index
        $rowIndex = 0;

        // Number of rows processed during import process.
        $processedRows = 0;

        // Total number of importable rows in the whole file.
        $totalNumberOfRows = 0;

        $skipRows = $importConfig->getNumberOfRowsToSkip() + $runtimeConfig->getOffset();
        $limit = $runtimeConfig->getLimit() + $skipRows;
        $isFirstIteration = $this->isFirstIteration($runtimeConfig);

        foreach ($this->fileReader->read($importFile) as $dataRow) {
            if ($isFirstIteration) {
                ++$totalNumberOfRows;
            }

            // Skip rows until the correct row is reached.
            if ($rowIndex < $skipRows) {
                ++$rowIndex;
                continue;
            }

            // If import process limit is reached - stop importing the rows.
            if ($rowIndex >= $limit) {
                // On the first iteration we need to continue counting the number of rows
                if ($isFirstIteration) {
                    continue;
                }
                break;
            }

            try {
                // Import one row
                $importHandler->importRow(
                    $importConfig,
                    $runtimeConfig,
                    $dataRow
                );
            } catch (InvalidDataRowException $e) {
                continue;
            } catch (SkippedIterationException $e) {
                continue;
            } finally {
                ++$processedRows;
                ++$rowIndex;
            }
        }

        // Calculate total number of rows only in the first import iteration.
        if ($isFirstIteration && $runtimeConfig->shouldValidateData()) {
            $runtimeConfig->setTotalNumberOfRows($totalNumberOfRows - $skipRows);
        }

        $runtimeConfig->setNumberOfProcessedRows($processedRows);

        $this->tearDown($importHandler, $importConfig, $runtimeConfig);
    }

    /**
     * Checks if data should be truncated.
     * Data should be truncated only when it's not validation step
     * and it's the first batch of the first process of the import.
     *
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     *
     * @return bool
     */
    public function shouldTruncateData(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig
    ) {
        return
            $importConfig->truncate() &&
            !$runtimeConfig->shouldValidateData() &&
            $this->isFirstIteration($runtimeConfig)
        ;
    }

    /**
     * Checks if current import iteration is the first.
     *
     * @param ImportRuntimeConfigInterface $runtimeConfig
     *
     * @return bool
     */
    private function isFirstIteration(ImportRuntimeConfigInterface $runtimeConfig)
    {
        return 0 === $runtimeConfig->getOffset();
    }

    /**
     * Set the import process up.
     *
     * @param ImportHandlerInterface $importHandler
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    private function setUp(
        ImportHandlerInterface $importHandler,
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig
    ) {
        if ($this->shouldTruncateData($importConfig, $runtimeConfig) && $this->accessChecker->canTruncateData()) {
            $this->entityDeleter->deleteAll($importConfig->getEntityType());
        }

        $importHandler->setUp($importConfig, $runtimeConfig);
    }

    /**
     * Tear the import process down.
     *
     * @param ImportHandlerInterface $importHandler
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    private function tearDown(
        ImportHandlerInterface $importHandler,
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig
    ) {
        $importHandler->tearDown($importConfig, $runtimeConfig);

        // Calculating shared data size and adding some extra bytes for other values.
        $runtimeConfig->setRequestSizeInBytes(
            mb_strlen(json_encode($runtimeConfig->getSharedData())) + 1024 * 64
        );
        $runtimeConfig->setPostSizeLimitInBytes($this->iniConfiguration->getUploadMaxSizeInBytes());
        $runtimeConfig->setNotices($importHandler->getNotices());
        $runtimeConfig->setWarnings($importHandler->getWarnings());
        $runtimeConfig->setErrors($importHandler->getErrors());
    }
}
