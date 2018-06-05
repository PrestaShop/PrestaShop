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

use PrestaShopBundle\Translation\TranslatorComponent as Translator;

class DataLangCore
{
    /** @var Translator  */
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

        $legacyTranslator = Context::getContext()->getTranslator();
        $legacyLocale = $legacyTranslator->getLocale();

        if ($legacyLocale === $this->locale) {
            $this->translator = $legacyTranslator;
        } else {
            $this->translator = new Translator(
                $this->locale,
                null,
                _PS_CACHE_DIR_ . '/translations/' . $this->locale,
                false
            );

            $this->translator->addLoader('xlf', new \Symfony\Component\Translation\Loader\XliffFileLoader());

            $finder = \Symfony\Component\Finder\Finder::create()
                ->files()
                ->name('*.' . $this->locale . '.xlf')
                ->in((_PS_ROOT_DIR_ . '/app/Resources/translations'));

            foreach ($finder as $file) {
                list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
                $this->translator->addResource($format, $file, $locale, $domain);
            }
        }
    }

    public function getFieldValue($field, $value)
    {
        return $this->translator->trans($value, array(), $this->domain, $this->locale);
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
