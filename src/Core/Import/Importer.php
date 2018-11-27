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

namespace PrestaShop\PrestaShop\Core\Import;

use PrestaShop\PrestaShop\Core\Import\Access\ImportAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;
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
     * @param ImportAccessCheckerInterface $accessChecker
     * @param ImportEntityDeleterInterface $entityDeleter
     * @param FileReaderInterface $fileReader
     * @param ImportDirectory $importDir
     */
    public function __construct(
        ImportAccessCheckerInterface $accessChecker,
        ImportEntityDeleterInterface $entityDeleter,
        FileReaderInterface $fileReader,
        ImportDirectory $importDir
    ) {
        $this->entityDeleter = $entityDeleter;
        $this->accessChecker = $accessChecker;
        $this->fileReader = $fileReader;
        $this->importDir = $importDir;
    }

    /**
     * {@inheritdoc}
     */
    public function import(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        ImportHandlerInterface $importHandler
    ) {
        if ($this->shouldTruncateData($importConfig, $runtimeConfig) && $this->accessChecker->canTruncateData()) {
            $this->entityDeleter->deleteAll($importConfig->getEntityType());
        }

        $importHandler->setUp($importConfig);
        $importFile = new SplFileInfo($this->importDir.$importConfig->getFileName());

        $rowIndex = 0;
        $skipRows = $importConfig->getNumberOfRowsToSkip() + $runtimeConfig->getOffset();
        $limit = $runtimeConfig->getLimit();

        foreach ($this->fileReader->read($importFile) as $dataRow) {
            // Skip rows until the correct row is reached.
            if ($rowIndex < $skipRows) {
                $rowIndex++;
                continue;
            }

            // If import process limit is reached - stop importing the rows.
            if ($rowIndex >= $limit) {
                break;
            }

            // Import one row
            $importHandler->importRow(
                $importConfig,
                $runtimeConfig,
                $dataRow
            );
        }

        $importHandler->tearDown();
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
        return $importConfig->truncate() && $this->isFirstIteration($runtimeConfig);
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
        return !$runtimeConfig->shouldValidateData() &&
            0 === $runtimeConfig->getOffset() &&
            0 === $runtimeConfig->getProcessIndex()
        ;
    }
}
