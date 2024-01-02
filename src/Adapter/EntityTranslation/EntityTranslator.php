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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

use DataLangCore;
use Db;
use InvalidArgumentException;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Translation\EntityTranslatorInterface;
use PrestaShopBundle\Translation\TranslatorInterface;

/**
 * Translates an entity in database using DataLang classes
 */
class EntityTranslator implements EntityTranslatorInterface
{
    /**
     * @var DataLangCore
     */
    protected $dataLang;

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @param Db $db
     * @param string $dbPrefix
     * @param TranslatorInterface $translator
     * @param DataLangCore $dataLang
     */
    public function __construct(
        Db $db,
        string $dbPrefix,
        TranslatorInterface $translator,
        DataLangCore $dataLang
    ) {
        $this->dataLang = $dataLang;
        $this->db = $db;
        $this->dbPrefix = $dbPrefix;
        $this->translator = $translator;
        $this->tableName = $this->buildTableNameFromDataLang($dataLang);
    }

    /**
     * Translate the entity's data in database using reverse translation technique
     *
     * @param int $languageId
     * @param int $shopId
     *
     * @throws LanguageNotFoundException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function translate(int $languageId, int $shopId): void
    {
        $lang = new Language($languageId);
        if (empty($lang->id)) {
            throw new LanguageNotFoundException(new LanguageId($languageId));
        }

        $tableNameSql = bqSQL($this->tableName);
        $shopWhere = ($this->shopFieldExists($tableNameSql))
            ? sprintf(' AND `id_shop` = %d', $shopId)
            : '';

        // get table data
        $sql = "SELECT * FROM `$tableNameSql` WHERE `id_lang` = $languageId"
            . $shopWhere;

        $tableData = $this->db->executeS($sql, true, false);

        if (empty($tableData)) {
            return;
        }

        $keys = $this->dataLang->getKeys();
        $fieldsToUpdate = $this->dataLang->getFieldsToUpdate();

        foreach ($tableData as $data) {
            $updateWhere = [];
            $updateFields = [];

            // Construct update where
            foreach ($keys as $key) {
                $updateWhere[] = '`' . bqSQL($key) . '` = "' . pSQL($data[$key]) . '"';
            }

            // Construct update field
            foreach ($fieldsToUpdate as $fieldName) {
                if ('url_rewrite' === $fieldName && Language::$locale_crowdin_lang === $lang->locale) {
                    continue;
                }

                $translatedField = $this->doTranslate($data, $fieldName);

                if (!empty($translatedField) && $translatedField != $data[$fieldName]) {
                    $updateFields[] = '`' . bqSQL($fieldName) . '` = "' . pSQL($translatedField) . '"';
                }
            }

            // Update table
            if (!empty($updateWhere) && !empty($updateFields)) {
                $updateWhere = implode(' AND ', $updateWhere);
                $updateFields = implode(', ', $updateFields);

                $sql = "UPDATE `$tableNameSql`
                    SET $updateFields
                    WHERE $updateWhere AND `id_lang` = $languageId"
                    . $shopWhere
                    . ' LIMIT 1';

                $this->db->execute($sql);
            }
        }
    }

    /**
     * Returns true if an id_shop field exists in database
     *
     * @param string $tableNameSql
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    protected function shopFieldExists(string $tableNameSql): bool
    {
        $columns = $this->db->executeS(
            sprintf('SHOW COLUMNS FROM `%s`', bqSQL($tableNameSql))
        );

        foreach ($columns as $column) {
            if ($column['Field'] === 'id_shop') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the original wording via reverse dictionary search (aka "untranslation")
     *
     * @param array $data Database record
     * @param string $fieldName Name of the field from $data to translate
     *
     * @return string "Untranslated" value
     */
    protected function getSourceString(array $data, string $fieldName): string
    {
        if (!array_key_exists($fieldName, $data)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to reverse translate entity field "%s" because it\'s not defined in the provided database record',
                    $fieldName
                )
            );
        }

        return (string) $this->translator->getSourceString($data[$fieldName], $this->dataLang->getDomain());
    }

    /**
     * Finds out the original wording and translates it
     *
     * @param array $data Database record
     * @param string $fieldName Name of the field from $data to translate
     *
     * @return string Translated value
     */
    protected function doTranslate(array $data, string $fieldName): string
    {
        $untranslated = $this->getSourceString($data, $fieldName);

        return $this->dataLang->getFieldValue($fieldName, $untranslated);
    }

    /**
     * Builds the table name using the DataLang class as source
     *
     * @param DataLangCore $dataLang
     *
     * @return string The table name, including prefix
     */
    private function buildTableNameFromDataLang(DataLangCore $dataLang): string
    {
        $tableName = $this->dataLang->getTableName();
        if (!str_starts_with($tableName, $this->dbPrefix)) {
            $tableName = $this->dbPrefix . $tableName;
        }

        return $tableName;
    }
}
