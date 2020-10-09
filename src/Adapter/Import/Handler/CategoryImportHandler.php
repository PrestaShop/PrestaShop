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

use Category;
use Doctrine\DBAL\Connection;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Import\ImageCopier;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\Exception\InvalidDataRowException;
use PrestaShop\PrestaShop\Core\Import\Exception\SkippedIterationException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use Psr\Log\LoggerInterface;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CategoryImportHandler holds legacy logic of category import.
 */
final class CategoryImportHandler extends AbstractImportHandler
{
    /**
     * @var array core categories IDs, such as Root and Home
     */
    private $coreCategories;

    /**
     * @var ImageCopier
     */
    private $imageCopier;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string database prefix
     */
    private $dbPrefix;

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
     * @param ImageCopier $imageCopier
     * @param Tools $tools
     * @param Connection $connection
     * @param string $dbPrefix
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
        Validate $validate,
        ImageCopier $imageCopier,
        Tools $tools,
        Connection $connection,
        $dbPrefix
    ) {
        parent::__construct(
            $dataFormatter,
            $allShopIds,
            $contextShopIds,
            $currentContextShopId,
            $isMultistoreEnabled,
            $contextLanguageId,
            $translator,
            $logger,
            $employeeId,
            $legacyDatabase,
            $cacheClearer,
            $configuration,
            $validate
        );
        $this->imageCopier = $imageCopier;
        $this->tools = $tools;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->importTypeLabel = $this->translator->trans('Categories', [], 'Admin.Global');
        $this->defaultValues = [
            'active' => '1',
            'parent' => $this->configuration->getInt('PS_HOME_CATEGORY'),
            'link_rewrite' => '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
    {
        parent::setUp($importConfig, $runtimeConfig);

        $this->coreCategories = [
            $this->configuration->getInt('PS_ROOT_CATEGORY'),
            $this->configuration->getInt('PS_HOME_CATEGORY'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    ) {
        parent::importRow($importConfig, $runtimeConfig, $dataRow);

        $entityFields = $runtimeConfig->getEntityFields();
        $categoryId = $this->fetchDataValueByKey($dataRow, $entityFields, 'id');

        $this->checkCategoryId($categoryId);

        if ($categoryId && ($importConfig->forceIds() || ObjectModel::existsInDatabase($categoryId, 'category'))) {
            $category = new Category((int) $categoryId);
        } else {
            $category = new Category();
        }

        $category->id_shop_default = $this->currentContextShopId;

        $this->setDefaultValues($category);
        $this->fillEntityData($category, $entityFields, $dataRow, $this->languageId);
        $this->findParentCategory($category, $runtimeConfig, $categoryId);
        $this->fillLinkRewrite($category, $categoryId);
        $this->createCategory(
            $category,
            $importConfig,
            $runtimeConfig,
            $categoryId,
            $this->fetchDataValueByKey($dataRow, $entityFields, 'name'),
            $this->fetchDataValueByKey($dataRow, $entityFields, 'shop')
        );

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
    {
        if (!$runtimeConfig->shouldValidateData() && $runtimeConfig->isFinished()) {
            /* Import has finished, we can regenerate the categories nested tree */
            Category::regenerateEntireNtree();
        }

        parent::tearDown($importConfig, $runtimeConfig);
    }

    /**
     * Checks if given category ID is allowed in the import.
     *
     * @param int $categoryId
     */
    private function checkCategoryId($categoryId)
    {
        if (in_array($categoryId, $this->coreCategories)) {
            $this->error(
                $this->translator->trans(
                    'The category ID must be unique. It can\'t be the same as the one for Root or Home category.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );
            throw new InvalidDataRowException();
        }
    }

    /**
     * Find the parent category for category that's being imported.
     *
     * @param Category $category
     * @param ImportRuntimeConfigInterface $runtimeConfig
     * @param int $categoryId
     */
    private function findParentCategory(
        Category $category,
        ImportRuntimeConfigInterface $runtimeConfig,
        $categoryId
    ) {
        if (!isset($category->parent)) {
            return;
        }

        $isValidation = $runtimeConfig->shouldValidateData();

        // Parent category
        if (is_numeric($category->parent)) {
            // Validation for parenting itself
            if ($isValidation && $category->parent == $category->id) {
                $this->error($this->translator->trans(
                'The category ID must be unique. It can\'t be the same as the one for the parent category (ID: %1$s).',
                    $categoryId ?: null,
                    'Admin.Advparameters.Notification'
                ));

                throw new InvalidDataRowException();
            }

            $sharedData = $runtimeConfig->getSharedData();
            $movedCategories = isset($sharedData['cat_moved']) ? $sharedData['cat_moved'] : [];

            if (isset($movedCategories[$category->parent])) {
                $category->parent = $movedCategories[$category->parent];
            }
            $category->id_parent = $category->parent;
        } elseif (is_string($category->parent)) {
            // Validation for parenting itself
            if ($isValidation && isset($category->name) && ($category->parent == $category->name)) {
                $this->error(
                    $this->translator->trans(
                        'A category can\'t be its own parent. You should rename it (current name: %1$s).',
                        [$category->parent],
                        'Admin.Advparameters.Notification'
                    )
                );

                throw new InvalidDataRowException();
            }
            $categoryParent = Category::searchByName($this->languageId, $category->parent, true);
            if ($categoryParent['id_category']) {
                $category->id_parent = (int) $categoryParent['id_category'];
                $category->level_depth = (int) $categoryParent['level_depth'] + 1;
            } else {
                $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
                $categoryToCreate = new Category();
                $categoryToCreate->name = $this->dataFormatter->createMultiLangField($category->parent);
                $categoryToCreate->active = 1;
                $linkRewrite = $this->dataFormatter->createFriendlyUrl(
                    $categoryToCreate->name[$this->languageId]
                );
                $categoryToCreate->link_rewrite = $this->dataFormatter->createMultiLangField($linkRewrite);
                // Default parent is home for unknown category to create
                $categoryToCreate->id_parent = $this->configuration->getInt('PS_HOME_CATEGORY');

                $fieldsError = $category->validateFields($unfriendlyError, true);
                $langFieldsError = $category->validateFieldsLang($unfriendlyError, true);
                $isValid = true === $fieldsError && true === $langFieldsError;

                if ($isValid && !$isValidation && $categoryToCreate->add()) {
                    $category->id_parent = $categoryToCreate->id;
                } else {
                    if (!$isValidation) {
                        $this->error(
                            $this->translator->trans(
                                '%category_name% (ID: %id%) cannot be saved',
                                [
                                    '%category_name%' => $categoryToCreate->name[$this->languageId],
                                    '%id%' => !empty($categoryToCreate->id) ? $categoryToCreate->id : 'null',
                                ],
                                'Admin.Advparameters.Notification'
                            )
                        );
                    }

                    if (!$isValid) {
                        $error = true !== $fieldsError ? $fieldsError : '';
                        $error .= true !== $langFieldsError ? $langFieldsError : '';

                        $this->error($error . $this->legacyDatabase->getErrorMessage());
                    }
                }
            }
        }
    }

    /**
     * Fill link rewrite value for category.
     *
     * @param Category $category
     * @param int $categoryId
     */
    private function fillLinkRewrite(Category $category, $categoryId)
    {
        if (isset($category->link_rewrite) && !empty($category->link_rewrite[$this->defaultLanguageId])) {
            $validLinkRewrite = $this->validate->isLinkRewrite($category->link_rewrite[$this->defaultLanguageId]);
            $linkRewrite = $category->link_rewrite[$this->defaultLanguageId];
        } else {
            $validLinkRewrite = false;
            $linkRewrite = $category->link_rewrite;
        }

        if (empty($linkRewrite) || !$validLinkRewrite) {
            $category->link_rewrite = $this->dataFormatter->createFriendlyUrl(
                $category->name[$this->defaultLanguageId]
            );
            if ($category->link_rewrite == '') {
                $category->link_rewrite = 'friendly-url-autogeneration-failed';
                $this->warning(
                    $this->translator->trans(
                        'URL rewriting failed to auto-generate a friendly URL for: %category_name%',
                        [
                            '%category_name%' => $category->name[$this->defaultLanguageId],
                        ],
                        'Admin.Advparameters.Notification'
                    )
                );
            }
            $category->link_rewrite = $this->dataFormatter->createMultiLangField($category->link_rewrite);
        }

        if (!$validLinkRewrite) {
            $this->notice(
                $this->translator->trans(
                    'Rewrite link for %1$s (ID %2$s): re-written as %3$s.',
                    [
                        '%1$s' => $linkRewrite,
                        '%2$s' => !empty($categoryId) ? $categoryId : 'null',
                        '%3$s' => $category->link_rewrite[$this->defaultLanguageId],
                    ],
                    'Admin.Advparameters.Notification'
                )
            );
        }
    }

    /**
     * Create the category.
     *
     * @param Category $category
     * @param ImportConfigInterface $importConfig
     * @param ImportRuntimeConfigInterface $runtimeConfig
     * @param int $categoryId
     * @param string $categoryName
     * @param string $shopData
     */
    private function createCategory(
        Category $category,
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        $categoryId,
        $categoryName,
        $shopData
    ) {
        $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
        $movedCategories = [];
        $result = false;

        $fieldsError = $category->validateFields($unfriendlyError, true);
        $langFieldsError = $category->validateFieldsLang($unfriendlyError, true);
        $isValid = true === $fieldsError && true === $langFieldsError;

        if ($isValid && empty($this->getErrors())) {
            $categoryAlreadyCreated = Category::searchByNameAndParentCategoryId(
                $this->languageId,
                $category->name[$this->languageId],
                $category->id_parent
            );

            // If category already in base, get id category back
            if ($categoryAlreadyCreated['id_category']) {
                $movedCategories[$category->id] = (int) $categoryAlreadyCreated['id_category'];
                $category->id = (int) $categoryAlreadyCreated['id_category'];
                if (Validate::isDate($categoryAlreadyCreated['date_add'])) {
                    $category->date_add = $categoryAlreadyCreated['date_add'];
                }
            }

            if ($category->id && $category->id == $category->id_parent) {
                $this->error(
                    sprintf(
                        $this->translator->trans(
                'A category cannot be its own parent. The parent category ID is either missing or unknown (ID: %1$s).',
                            [],
                            'Admin.Advparameters.Notification'
                        ),
                        !empty($categoryId) ? $categoryId : 'null'
                    )
                );

                throw new InvalidDataRowException();
            }

            /* No automatic nTree regeneration for import */
            $category->doNotRegenerateNTree = true;

            // If id category AND id category already in base, trying to update
            if ($category->id &&
                $category->categoryExists($category->id) &&
                !in_array($category->id, $this->coreCategories) &&
                !$runtimeConfig->shouldValidateData()
            ) {
                $result = $category->update();
            }
            if ($category->id == $this->configuration->getInt('PS_ROOT_CATEGORY')) {
                $this->error(
                    $this->translator->trans(
                        'The root category cannot be modified.',
                        [],
                        'Admin.Advparameters.Notification'
                    )
                );
            }
            // If no id_category or update failed
            $category->force_id = (bool) $importConfig->forceIds();
            if (!$result && !$runtimeConfig->shouldValidateData()) {
                $result = $category->add();
                if ($categoryId && $category->id != $categoryId) {
                    $movedCategories[$categoryId] = $category->id;
                }
            }
        }

        if ($movedCategories) {
            $sharedData = $runtimeConfig->getSharedData();

            if ($this->propertyAccessor->isWritable($sharedData, '[cat_moved]')) {
                $sharedItem = $this->propertyAccessor->getValue($sharedData, '[cat_moved]');
                $sharedItem = is_array($sharedItem) ? $sharedItem + $movedCategories : $movedCategories;
                $runtimeConfig->addSharedDataItem('cat_moved', $sharedItem);
            }
        }

        if ($runtimeConfig->shouldValidateData()) {
            throw new SkippedIterationException();
        }

        //copying images of categories
        if (!empty($category->image)) {
            $copyResult = $this->imageCopier->copyImg(
                $category->id,
                null,
                $category->image,
                'categories',
                !$importConfig->skipThumbnailRegeneration()
            );

            if (!$copyResult) {
                $this->warning(
                    $category->image .
                    ' ' .
                    $this->translator->trans('cannot be copied.', [], 'Admin.Advparameters.Notification')
                );
            }
        }

        // If both failed, mysql error
        if (!$result) {
            $this->error(
                $this->translator->trans(
                    '%1$s (ID: %2$s) cannot be %3$s',
                    [
                        !empty($categoryName) ? $this->tools->sanitize($categoryName) : 'No Name',
                        !empty($categoryId) ? $this->tools->sanitize($categoryId) : 'No ID',
                        $runtimeConfig->shouldValidateData() ? 'validated' : 'saved',
                    ],
                    'Admin.Advparameters.Notification'
                )
            );
            $error = $fieldsError !== true ? $fieldsError : '';
            $error .= $langFieldsError !== true ? $langFieldsError : '';
            $error .= $this->legacyDatabase->getErrorMessage();

            if ($error) {
                $this->error($error);
            }
        } else {
            // Associate category to shop
            if ($this->isMultistoreEnabled) {
                $this->connection->delete(
                    $this->dbPrefix . 'category_shop',
                    [
                        'id_category' => (int) $category->id,
                    ]
                );

                if (empty($shopData)) {
                    $shopData = implode($importConfig->getMultipleValueSeparator(), $this->contextShopIds);
                }

                // Get shops for each attributes
                $shopData = explode($importConfig->getMultipleValueSeparator(), $shopData);

                foreach ($shopData as $shop) {
                    if (!empty($shop)) {
                        if (!is_numeric($shop)) {
                            $category->addShop(Shop::getIdByName($shop));
                        } else {
                            $category->addShop($shop);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($importEntityType)
    {
        return $importEntityType === Entity::TYPE_CATEGORIES;
    }
}
