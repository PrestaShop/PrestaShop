<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

use DataLangCore;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\Exception\DataLangClassNameNotFoundException;
use PrestaShop\PrestaShop\Core\Util\Inflector;
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

        return Inflector::getInflector()->classify($tableName);
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
            $tableName = substr($tableName, $length) ?: '';
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
        if (!str_ends_with($tableName, '_lang')) {
            $tableName .= '_lang';
        }

        return $tableName;
    }
}
