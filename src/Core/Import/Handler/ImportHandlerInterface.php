<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Handler;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\EmptyDataRowException;
use PrestaShop\PrestaShop\Core\Import\Exception\SkippedIterationException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;

/**
 * Interface ImportHandlerInterface describes an import handler.
 */
interface ImportHandlerInterface
{
    /**
     * Executed before import process is started.
     *
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    public function setUp(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig);

    /**
     * Imports one data row.
     *
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     * @param DataRowInterface $dataRow
     *
     * @throws EmptyDataRowException
     * @throws SkippedIterationException
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    );

    /**
     * Executed when the import process is completed.
     *
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    public function tearDown(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig);

    /**
     * Get warning messages that occurred during import.
     *
     * @return array
     */
    public function getWarnings();

    /**
     * Get error messages that occurred during import.
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get notice messages that occurred during import.
     *
     * @return array
     */
    public function getNotices();

    /**
     * Check whether this import handler supports given entity type.
     *
     * @param int $importEntityType
     *
     * @return bool
     */
    public function supports($importEntityType);
}
