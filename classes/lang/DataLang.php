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

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Translation\TranslatorInterface;

/**
 * DataLang classes are used by Language
 * to update existing entities in the database whenever a new language is installed.
 * Each *Lang subclass corresponds to a database table.
 *
 * @see Language::updateMultilangFromClass()
 */
class DataLangCore
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string Locale to translate to */
    protected $locale;

    /** @var string[] Table primary key */
    protected $keys;

    /** @var string[] Database fields to translate */
    protected $fieldsToUpdate;

    /** @var string Default translation domain */
    protected $domain;

    /**
     * @param string $locale
     * @param TranslatorInterface|null $translator If defined, use this translator
     */
    public function __construct($locale, ?TranslatorInterface $translator = null)
    {
        $this->locale = $locale;

        $this->translator = $translator instanceof TranslatorInterface
            ? $translator
            : SymfonyContainer::getInstance()->get(TranslatorInterface::class);

        $isAdminContext = defined('_PS_ADMIN_DIR_');

        if (!$this->translator->isLanguageLoaded($this->locale)) {
            SymfonyContainer::getInstance()->get('prestashop.translation.translator_language_loader')
                ->setIsAdminContext($isAdminContext)
                ->loadLanguage($this->translator, $this->locale);
            $this->translator->getCatalogue($this->locale);
        }
    }

    /**
     * Translates a value to the current locale
     *
     * @param string $field Name of the database field to translate
     * @param string $value Value to translate
     *
     * @return string Translated value
     */
    public function getFieldValue($field, $value)
    {
        return $this->translator->trans($value, [], $this->domain, $this->locale);
    }

    /**
     * Returns the table primary key
     *
     * @return string[]
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * Returns the list of database fields to update
     *
     * @return string[]
     */
    public function getFieldsToUpdate()
    {
        return $this->fieldsToUpdate;
    }

    /**
     * Creates a slug from the provided string
     *
     * @param string $string
     *
     * @return string
     */
    public function slugify($string)
    {
        return strtolower(str_replace(' ', '-', Tools::replaceAccentedChars($string)));
    }

    /**
     * Returns the default translation domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns the table name where the translations are to be performed
     *
     * @return string
     */
    public function getTableName(): string
    {
        $shortClassName = substr(strrchr('\\' . get_class($this), '\\'), 1);

        return Inflector::getInflector()->tableize($shortClassName);
    }
}
