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
    private $stepIndex;

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
     * {@inheritdoc}
     */
    public function setNotices(array $notices)
    {
        $this->notices = $notices;
    }

    /**
     * {@inheritdoc}
     */
    public function setWarnings(array $warnings)
    {
        $this->warnings = $warnings;
    }

    /**
     * {@inheritdoc}
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
