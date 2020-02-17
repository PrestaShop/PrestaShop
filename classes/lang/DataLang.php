<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use PrestaShopBundle\Translation\TranslatorLanguageLoader;

/**
 * DataLang classes are used by Language
 * to update existing entities in the database whenever a new language is installed.
 * Each *Lang subclass corresponds to a database table.
 *
 * @see Language::updateMultilangFromClass()
 */
class DataLangCore
{
    /** @var Translator */
    protected $translator;

    /** @var string */
    protected $locale;

    /** @var array */
    protected $keys;

    /** @var array */
    protected $fieldsToUpdate;

    /** @var string */
    protected $domain;

    public function __construct($locale)
    {
        $this->locale = $locale;

        $this->translator = SymfonyContainer::getInstance()->get('translator');
        $isAdminContext = defined('_PS_ADMIN_DIR_');

        if (!$this->translator->isLanguageLoaded($this->locale)) {
            (new TranslatorLanguageLoader($isAdminContext))->loadLanguage($this->translator, $this->locale);
            $this->translator->getCatalogue($this->locale);
        }
    }

    public function getFieldValue($field, $value)
    {
        return $this->translator->trans($value, [], $this->domain, $this->locale);
    }

    public function getKeys()
    {
        return $this->keys;
    }

    public function getFieldsToUpdate()
    {
        return $this->fieldsToUpdate;
    }

    public function slugify($string)
    {
        return strtolower(str_replace(' ', '-', Tools::replaceAccentedChars($string)));
    }

    public function getDomain()
    {
        return $this->domain;
    }
}
