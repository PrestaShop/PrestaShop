<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Class ImportRuntimeConfig defines import runtime configuration.
 */
final class ImportRuntimeConfig implements ImportRuntimeConfigInterface
{
    /**
     * @var bool
     */
    private $shouldValidateData;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array import entity fields mapping
     */
    private $entityFields;

    /**
     * @var array
     */
    private $sharedData = [];

    /**
     * @var int
     */
    private $processedRows = 0;

    /**
     * @var int request size in bytes
     */
    private $requestSize;

    /**
     * @var int post size limit in bytes
     */
    private $postSizeLimit;

    /**
     * @var int total number of rows to be imported
     */
    private $totalNumberOfRows;

    /**
     * @var array
     */
    private $notices;

    /**
     * @var array
     */
    private $warnings;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param bool $shouldValidateData
     * @param int $offset
     * @param int $limit
     * @param array $sharedData
     * @param array $entityFields
     */
    public function __construct(
        $shouldValidateData,
        $offset,
        $limit,
        array $sharedData,
        array $entityFields
    ) {
        $this->shouldValidateData = $shouldValidateData;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->entityFields = $entityFields;
        $this->sharedData = $sharedData;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldValidateData()
    {
        return $this->shouldValidateData;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityFields()
    {
        return $this->entityFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getSharedData()
    {
        return $this->sharedData;
    }

    /**
     * {@inheritdoc}
     */
    public function addSharedDataItem($key, $value)
    {
        $this->sharedData[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinished()
    {
        return $this->processedRows < $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumberOfProcessedRows($number)
    {
        $this->processedRows = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfProcessedRows()
    {
        return $this->processedRows;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestSizeInBytes($size)
    {
        $this->requestSize = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostSizeLimitInBytes($size)
    {
        $this->postSizeLimit = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNumberOfRows($number)
    {
        $this->totalNumberOfRows = $number;
    }

    /**
     * @param array $notices
     *
     * @return array|void
     */
    public function setNotices(array $notices)
    {
        $this->notices = $notices;
    }

    /**
     * @param array $warnings
     *
     * @return array|void
     */
    public function setWarnings(array $warnings)
    {
        $this->warnings = $warnings;
    }

    /**
     * @param array $errors
     *
     * @return array|void
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'crossStepsVariables' => $this->sharedData,
            'doneCount' => $this->processedRows + $this->offset,
            'isFinished' => $this->isFinished(),
            'nextPostSize' => $this->requestSize,
            'postSizeLimit' => $this->postSizeLimit,
            'totalCount' => $this->totalNumberOfRows,
            'notices' => $this->notices,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
        ];
    }
}
