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

namespace PrestaShop\PrestaShop\Adapter\Import\Handler;

use Language;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\EmptyDataRowException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractImportHandler is an abstract handler for import.
 */
abstract class AbstractImportHandler implements ImportHandlerInterface
{
    /**
     * @var ImportDataFormatter
     */
    protected $dataFormatter;

    /**
     * @var array
     */
    protected $contextShopIds;

    /**
     * @var bool whether the multistore feature is enabled
     */
    protected $isMultistoreEnabled;

    /**
     * @var int
     */
    protected $currentContextShopId;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array all shops ids
     */
    protected $allShopIds;

    /**
     * @var string import type label
     */
    protected $importTypeLabel;

    /**
     * @var Database
     */
    protected $legacyDatabase;

    /**
     * @var int
     */
    protected $languageId;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Validate
     */
    protected $validate;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var int
     */
    protected $defaultLanguageId;

    /**
     * @var array entity default values
     */
    protected $defaultValues = [];

    /**
     * Callback methods with field names as keys.
     * Callback methods are executed on fields during import process.
     *
     * @var array
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
     * @var array
     */
    private $warnings = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $notices = [];

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int employee ID, used for logs
     */
    private $employeeId;

    /**
     * @var CacheClearerInterface
     */
    private $cacheClearer;

    /**
     * @param ImportDataFormatter $dataFormatter
     * @param array $allShopIds
     * @param array $contextShopIds
     * @param int $currentContextShopId
     * @param bool $isMultistoreEnabled
     * @param int $contextLanguageId
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param int $employeeId
     * @param Database $legacyDatabase
     * @param CacheClearerInterface $cacheClearer
     * @param Configuration $configuration
     * @param Validate $validate
     */
    public function __construct(
        ImportDataFormatter $dataFormatter,
        array $allShopIds,
        array $contextShopIds,
        $currentContextShopId,
        $isMultistoreEnabled,
        $contextLanguageId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $employeeId,
        Database $legacyDatabase,
        CacheClearerInterface $cacheClearer,
        Configuration $configuration,
        Validate $validate
    ) {
        $this->dataFormatter = $dataFormatter;
        $this->contextShopIds = $contextShopIds;
        $this->currentContextShopId = $currentContextShopId;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
        $this->translator = $translator;
        $this->allShopIds = $allShopIds;
        $this->contextLanguageId = $contextLanguageId;
        $this->logger = $logger;
        $this->employeeId = $employeeId;
        $this->legacyDatabase = $legacyDatabase;
        $this->cacheClearer = $cacheClearer;
        $this->configuration = $configuration;
        $this->validate = $validate;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->defaultLanguageId = $this->configuration->getInt('PS_LANG_DEFAULT');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
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

        $this->legacyDatabase->disableCache();
    }

    /**
     * {@inheritdoc}
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    ) {
        if ($dataRow->isEmpty()) {
            $this->warning(
                $this->translator->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );
            throw new EmptyDataRowException();
        }

        if (!$this->languageId) {
            $this->languageId = Language::getIdByIso($importConfig->getLanguageIso());

            if (!$this->validate->isUnsignedInt($this->languageId)) {
                $this->languageId = $this->configuration->getInt('PS_LANG_DEFAULT');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
    {
        if (!$runtimeConfig->shouldValidateData()) {
            $offset = $runtimeConfig->getOffset();

            $logMessage = sprintf(
                $this->translator->trans('%s import', [], 'Admin.Advparameters.Notification'),
                $this->importTypeLabel
            );
            $logMessage .= ' ';
            $logMessage .= sprintf(
                $this->translator->trans('(from %s to %s)', [], 'Admin.Advparameters.Notification'),
                $offset,
                $runtimeConfig->getNumberOfProcessedRows() + $offset
            );
            if ($importConfig->truncate()) {
                $logMessage .= ' ';
                $logMessage .= $this->translator->trans('with truncate', [], 'Admin.Advparameters.Notification');
            }
            $this->logger->notice(
                $logMessage,
                [
                    'allow_duplicate' => true,
                    'object_type' => $this->importTypeLabel,
                ]
            );

            if ($runtimeConfig->isFinished()) {
                $this->cacheClearer->clear();
            }
        }

        $this->legacyDatabase->enableCache();
    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Add a warning message.
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->warnings[] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error message.
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->errors[] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * Add a notice message.
     *
     * @param string $message
     */
    public function notice($message)
    {
        $this->notices[] = $message;
    }

    /**
     * Fetch a data value by given entity field name out of data row.
     *
     * @param DataRowInterface $dataRow
     * @param array $entityFields required to find the data cell index in data row
     * @param string $entityFieldName
     *
     * @return string data value
     */
    protected function fetchDataValueByKey(DataRowInterface $dataRow, array $entityFields, $entityFieldName)
    {
        $cellIndex = array_search($entityFieldName, $entityFields);

        if (false !== $cellIndex && $dataRow->offsetExists($cellIndex)) {
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

        foreach ($this->defaultValues as $field => $defaultValue) {
            $fieldExists = array_key_exists($field, $members);
            if (!$fieldExists || $entity->$field === null) {
                $entity->$field = $defaultValue;
            }
        }
    }

    /**
     * Fill entity data out of data row.
     *
     * @param ObjectModel $entity
     * @param array $entityFields
     * @param DataRowInterface $dataRow
     * @param int $languageId
     */
    protected function fillEntityData(
        ObjectModel $entity,
        array $entityFields,
        DataRowInterface $dataRow,
        $languageId
    ) {
        foreach ($entityFields as $field) {
            $value = $this->fetchDataValueByKey($dataRow, $entityFields, $field);

            if (isset($this->callbacks[$field])) {
                $value = $this->callbacks[$field]($value);
            }

            $canBeTranslated = in_array($field, $this->languageFields) && $languageId;

            if ($canBeTranslated) {
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

    /**
     * Add a warning message with additional entity data.
     *
     * @param string $message
     * @param string $entityName
     * @param int|null $entityId
     */
    protected function addEntityWarning($message, $entityName, $entityId = null)
    {
        $this->warning(sprintf(
            '%s (ID %s) %s',
            (string) $entityName,
            null !== $entityId ? (int) $entityId : '',
            $message
        ));
    }

    /**
     * Checks if entity exists in the database.
     *
     * @param ObjectModel $entity
     * @param string $table database table without prefix, e.g. "product".
     *
     * @return bool
     */
    protected function entityExists(ObjectModel $entity, $table)
    {
        return $entity->id && ObjectModel::existsInDatabase($entity->id, $table);
    }
}
