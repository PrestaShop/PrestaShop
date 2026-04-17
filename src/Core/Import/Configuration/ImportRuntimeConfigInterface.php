<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
