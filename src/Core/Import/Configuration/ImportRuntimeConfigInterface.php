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
     * Get current import process index.
     *
     * @return int
     */
    public function getProcessIndex();

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
     * Increment the current import process index by one.
     */
    public function incrementProcessIndex();

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
     * @param $number
     */
    public function setTotalNumberOfRows($number);

    /**
     * Convert object to an array.
     *
     * @return array
     */
    public function toArray();
}
