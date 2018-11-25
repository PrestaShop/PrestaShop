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

namespace PrestaShop\PrestaShop\Core\Import\Handler;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;

/**
 * Interface ImportHandlerInterface describes an import handler.
 */
interface ImportHandlerInterface
{
    /**
     * Executed before any other import step, to validate the import data.
     */
    public function validate();

    /**
     * Executed before import process is started.
     * After the validation step.
     *
     * @param ImportConfigInterface $importConfig
     */
    public function setUp(ImportConfigInterface $importConfig);

    /**
     * Imports one data row.
     *
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     * @param DataRowInterface $dataRow
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    );

    /**
     * Executed when the import process is completed.
     */
    public function tearDown();

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
}
