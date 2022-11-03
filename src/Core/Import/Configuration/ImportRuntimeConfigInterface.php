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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Interface ImportRuntimeConfigInterface describes import runtime configuration.
 * Since import is a multi-process operation, this configuration can change
 * during each import process.
 */
interface ImportRuntimeConfigInterface
{
    /**
     * Checks if import should validate the data instead of importing it.
     *
     * @return bool
     */
    public function shouldValidateData();

    /**
     * Get current import offset.
     * Works similarly to SQL offset.
     *
     * @return int
     */
    public function getOffset();

    /**
     * Get current import limit.
     * Similar to SQL limit.
     *
     * @return int
     */
    public function getLimit();

    /**
     * Get the data, that is shared between import processes.
     *
     * @return array
     */
    public function getSharedData();

    /**
     * Add a shared data item.
     *
     * @param string $key
     * @param mixed $value
     */
    public function addSharedDataItem($key, $value);

    /**
     * Get import entity fields.
     *
     * @return array
     */
    public function getEntityFields();

    /**
     * Get number of rows processed in current import iteration.
     *
     * @return int
     */
    public function getNumberOfProcessedRows();

    /**
     * Check if the import is completely finished.
     *
     * @return bool
     */
    public function isFinished();

    /**
     * Set number of rows processed during import process runtime.
     *
     * @param int $number
     */
    public function setNumberOfProcessedRows($number);

    /**
     * Set request size in bytes.
     *
     * @param int $size
     */
    public function setRequestSizeInBytes($size);

    /**
     * Set post size limit in bytes.
     *
     * @param int $size
     */
    public function setPostSizeLimitInBytes($size);

    /**
     * Set the total number of rows to be imported.
     *
     * @param int $number
     */
    public function setTotalNumberOfRows($number);

    /**
     * Set notices that occurred during the import process.
     *
     * @param array $notices
     *
     * @return array
     */
    public function setNotices(array $notices);

    /**
     * Set warnings that occurred during the import process.
     *
     * @param array $warnings
     *
     * @return array
     */
    public function setWarnings(array $warnings);

    /**
     * Set errors that occurred during the import process.
     *
     * @param array $errors
     *
     * @return array
     */
    public function setErrors(array $errors);

    /**
     * Convert object to an array.
     *
     * @return array
     */
    public function toArray();
}
