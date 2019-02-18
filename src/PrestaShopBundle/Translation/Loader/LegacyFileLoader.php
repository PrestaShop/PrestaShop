<?php

/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Loader;

use PrestaShopBundle\Translation\Locale\Converter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * Able to convert old translation files (in translations/es.php) into
 * Symfony MessageCatalogue objects.
 */
final class LegacyFileLoader implements LoaderInterface
{
    /**
     * @var string the expected format of a legacy translation key
     */
    const LEGACY_TRANSLATION_FORMAT = '#\<\{([\w-]+)\}prestashop\>([\w-]+)_([\w-]+)#';

    /**
     * {@inheritdoc}
     */
    public function load($path, $locale, $domain = 'messages')
    {
        global $_MODULE;
        $catalogue = new MessageCatalogue($locale);
        $shopLocale = Converter::toLegacyLocale($locale);
        $filepath = $path . "$shopLocale.php";

        if (!file_exists($filepath)) {
            throw new \Exception('There is no translation file available in path: ' . $filepath);
        }

        // Load a global array $_MODULE
        include_once $filepath;

        foreach ($_MODULE as $translationKey => $translationValue) {
            $id = $this->getId($translationKey);
            $catalogue->set($id, $translationValue, $domain);
        }

        return $catalogue;
    }

    /**
     * @param string $key the translation key
     *
     * @return string the translation id
     */
    private function getId($key)
    {
        preg_match_all(self::LEGACY_TRANSLATION_FORMAT, $key, $params);

        return Container::camelize($params[3][0]);
    }
}
