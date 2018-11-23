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
     * @var int
     */
    private $processIndex;

    /**
     * @var array import entity fields mapping.
     */
    private $entityFields;

    /**
     * @var array
     */
    private $sharedData;

    /**
     * @param bool $shouldValidateData
     * @param int $offset
     * @param int $limit
     * @param int $processIndex
     * @param array $sharedData
     * @param array $entityFields
     */
    public function __construct(
        $shouldValidateData,
        $offset,
        $limit,
        $processIndex,
        array $sharedData,
        array $entityFields
    ) {
        $this->shouldValidateData = $shouldValidateData;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->processIndex = $processIndex;
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
    public function getProcessIndex()
    {
        return $this->processIndex;
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
}
