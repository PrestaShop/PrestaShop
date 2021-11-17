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
use Doctrine\Common\Inflector\Inflector;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\Exception\DataLangClassNameNotFoundException;
use PrestaShopBundle\Translation\TranslatorInterface;

/**
 * Builds instances of DataLang classes
 */
class DataLangFactory
{
    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param string $dbPrefix
     * @param TranslatorInterface $translator
     */
    public function __construct(string $dbPrefix, TranslatorInterface $translator)
    {
        $this->dbPrefix = $dbPrefix;
        $this->translator = $translator;
    }

    /**
     * Returns the appropriate DataLang class name using a table name as source. Note: the class may not exist.
     *
     * @param string $tableName Table name, accepts with and without db prefix and _lang suffix
     *
     * @return string dataLang class name
     */
    public function getClassNameFromTable(string $tableName): string
    {
        $tableName = $this->removeDbPrefixIfPresent($tableName);
        $tableName = $this->ensureLangSuffix($tableName);

        return Inflector::classify($tableName);
    }

    /**
     * Instantiates the appropriate DataLang class for the provided locale
     *
     * @param string $className Class name to instantiate
     * @param string $locale IETF language tag
     *
     * @return DataLangCore
     *
     * @throws DataLangClassNameNotFoundException
     */
    public function buildFromClassName(string $className, string $locale): DataLangCore
    {
        if (!class_exists($className)) {
            throw new DataLangClassNameNotFoundException(sprintf("Class name \"%s\" doesn't exist", $className));
        }

        /** @var DataLangCore $classObject */
        $classObject = new $className($locale, $this->translator);

        return $classObject;
    }

    /**
     * Instantiates the appropriate DataLang class for the provided table name and locale code
     *
     * @param string $tableName Table name (accepts with and without db prefix and _lang suffix)
     * @param string $locale IETF language tag
     *
     * @return DataLangCore
     */
    public function buildFromTableName(string $tableName, string $locale): DataLangCore
    {
        return $this->buildFromClassName($this->getClassNameFromTable($tableName), $locale);
    }

    /**
     * Removes the db prefix from the table name if present
     *
     * @param string $tableName
     *
     * @return string
     */
    private function removeDbPrefixIfPresent(string $tableName): string
    {
        $length = strlen($this->dbPrefix);
        if (substr($tableName, 0, $length) === $this->dbPrefix) {
            $tableName = substr($tableName, $length - 1) ?? '';
        }

        return $tableName;
    }

    /**
     * Adds the _lang suffix if not present
     *
     * @param string $tableName
     *
     * @return string
     */
    private function ensureLangSuffix(string $tableName): string
    {
        if (substr($tableName, -5) !== '_lang') {
            $tableName .= '_lang';
        }

        return $tableName;
    }
}
