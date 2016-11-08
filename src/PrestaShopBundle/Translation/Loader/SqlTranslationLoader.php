<?php

/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Loader;

use Exception;
use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class SqlTranslationLoader implements LoaderInterface
{
    /**
     * @var  Theme
     */
    protected $theme;

    /**
     * @param $theme
     * @return $this
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $locale = Db::getInstance()->escape($locale, false, true);
        $localeResult = Db::getInstance()->getRow('
            SELECT `id_lang`
            FROM `'._DB_PREFIX_.'lang`
            WHERE `locale` = "'.$locale.'"'
        );

        if (empty($localeResult)) {
            throw new Exception(sprintf('Language not found in database: %s', $locale));
        }

        $selectTranslationsQuery = '
            SELECT `key`, `translation`, `domain`
            FROM `'._DB_PREFIX_.'translation`
            WHERE `id_lang` = '.$localeResult['id_lang']
        ;
        $translations = Db::getInstance()->executeS($selectTranslationsQuery);

        $catalogue = new MessageCatalogue($locale);
        $this->addTranslationsToCatalogue($translations, $catalogue);

        if (!is_null($this->theme)) {
            $selectThemeTranslationsQuery =
                $selectTranslationsQuery."\n".
                "AND theme = '".$this->theme->getName()."'"
            ;
            $themeTranslations = Db::getInstance()->executeS($selectThemeTranslationsQuery);
            $this->addTranslationsToCatalogue($themeTranslations, $catalogue);
        }

        return $catalogue;
    }

    /**
     * @param $translations
     * @param $catalogue
     */
    protected function addTranslationsToCatalogue($translations, MessageCatalogueInterface $catalogue)
    {
        foreach ($translations as $translation) {
            $catalogue->set($translation['key'], $translation['translation'], $translation['domain']);
        }
    }
}
