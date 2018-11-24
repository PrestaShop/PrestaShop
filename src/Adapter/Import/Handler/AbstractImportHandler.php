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

namespace PrestaShop\PrestaShop\Adapter\Import\Handler;

use Language;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerInterface;

/**
 * Class AbstractImportHandler is an abstract handler for import.
 */
abstract class AbstractImportHandler implements ImportHandlerInterface
{
    /**
     * Callback methods with field names as keys.
     * Callback methods are executed on fields during import process.
     *
     * @var array.
     */
    private $callbacks = [];

    /**
     * Multilingual entity fields.
     *
     * @var array
     */
    private $languageFields = [
        'name',
        'description',
        'description_short',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'link_rewrite',
        'available_now',
        'available_later',
        'delivery_in_stock',
        'delivery_out_stock',
    ];

    /**
     * @var array entity default values
     */
    private $defaultValues = [];
    /**
     * @var ImportDataFormatter
     */
    private $dataFormatter;

    /**
     * @param ImportDataFormatter $dataFormatter
     */
    public function __construct(ImportDataFormatter $dataFormatter)
    {
        $this->dataFormatter = $dataFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(ImportConfigInterface $importConfig)
    {
        $languageIso = trim($importConfig->getLanguageIso());
        $locale = strtolower($languageIso) . '_' . strtoupper($languageIso) . '.UTF-8';
        setlocale(LC_COLLATE, $locale);
        setlocale(LC_CTYPE, $locale);

        $dataFormatter = $this->dataFormatter;
        $multipleValueSeparator = $importConfig->getMultipleValueSeparator();

        $getBoolean = function ($value) use ($dataFormatter) {
            return $dataFormatter->getBoolean($value);
        };
        $getPrice = function ($value) use ($dataFormatter) {
            return $dataFormatter->getPrice($value);
        };
        $createMultilangField = function ($value) use ($dataFormatter) {
            return $dataFormatter->createMultiLangField($value);
        };
        $split = function ($value) use ($dataFormatter, $multipleValueSeparator) {
            return $dataFormatter->split($value, $multipleValueSeparator);
        };
        $this->callbacks = [
            'active' => $getBoolean,
            'tax_rate' => $getPrice,
            'price_tex' => $getPrice,
            'price_tin' => $getPrice,
            'reduction_price' => $getPrice,
            'reduction_percent' => $getPrice,
            'wholesale_price' => $getPrice,
            'ecotax' => $getPrice,
            'name' => $createMultilangField,
            'description' => $createMultilangField,
            'description_short' => $createMultilangField,
            'meta_title' => $createMultilangField,
            'meta_keywords' => $createMultilangField,
            'meta_description' => $createMultilangField,
            'link_rewrite' => $createMultilangField,
            'available_now' => $createMultilangField,
            'available_later' => $createMultilangField,
            'category' => $split,
            'online_only' => $getBoolean,
            'accessories' => $split,
            'image_alt' => $split,
            'delivery_in_stock' => $createMultilangField,
            'delivery_out_stock' => $createMultilangField,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {

    }

    /**
     * Fetch a data value by given entity field name out of data row.
     *
     * @param DataRowInterface $dataRow
     * @param array $entityFields required to find the data cell index in data row.
     * @param string $entityFieldName
     *
     * @return string data value
     */
    protected function fetchDataValueByKey(DataRowInterface $dataRow, array $entityFields, $entityFieldName)
    {
        $cellIndex = array_search($entityFieldName, $entityFields);

        if ($dataRow->offsetExists($cellIndex)) {
            $dataCell = $dataRow->offsetGet($cellIndex);

            return trim($dataCell->getValue());
        }

        return '';
    }

    /**
     * Set default values for entity.
     *
     * @param ObjectModel $entity
     */
    protected function setDefaultValues(ObjectModel $entity)
    {
        $members = get_object_vars($entity);
        foreach ($this->defaultValues as $k => $v) {
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members)) {
                $entity->$k = $v;
            }
        }
    }

    /**
     * Fill entity data out of data row.
     *
     * @param ObjectModel $entity
     * @param array $entityFields
     * @param DataRowInterface $dataRow
     * @param string $languageIso
     */
    protected function fillEntityData(
        ObjectModel $entity,
        array $entityFields,
        DataRowInterface $dataRow,
        $languageIso
    ) {
        $languageId = Language::getIdByIso($languageIso);

        foreach ($entityFields as $field) {
            $value = $this->fetchDataValueByKey($dataRow, $entityFields, $field);

            if (isset($this->callbacks[$field])) {
                $value = $this->callbacks[$field]($value);
            }

            if (in_array($field, $this->languageFields) && $languageId) {
                foreach ($value as $langId => $formattedValue) {
                    if (empty($entity->{$field}[$languageId]) || $langId == $languageId) {
                        $entity->{$field}[$langId] = $formattedValue;
                    }
                }
            } elseif (!empty($value) || $value == '0') {
                $entity->{$field} = $value;
            }
        }
    }
}
