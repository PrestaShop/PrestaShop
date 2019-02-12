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

namespace PrestaShopBundle\Translation\Extractor;

use PrestaShopBundle\Translation\Locale\Converter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Able to convert old translation files (in translations/es.php) into
 * Symfony MessageCatalogue objects.
 */
final class LegacyFileExtractor implements LegacyFileExtractorInterface
{
    /**
     * @var string the expected format of a legacy translation key
     */
    const LEGACY_TRANSLATION_FORMAT = '#\<\{([\w-]+)\}prestashop\>([\w-]+)_([\w-]+)#';

    /**
     * @param string $path
     * @param string $locale
     *
     * @throws \Exception
     *
     * @return MessageCatalogueInterface
     */
    public function extract($path, $locale)
    {
        $_MODULE = [];
        $catalogue = new MessageCatalogue($locale);
        $shopLocale = Converter::toPrestaShopLocale($locale);
        $filepath = $path . "$shopLocale.php";

        if (!file_exists($filepath)) {
            throw new \Exception('There is no translation file available.');
        }

        include_once $filepath;

        foreach ($_MODULE as $translationKey => $translationValue) {
            $domain = $this->getDomain($translationKey);
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

    /**
     * @param string $key the translation key
     *
     * @return string the translation domain
     */
    private function getDomain($key)
    {
        preg_match_all(self::LEGACY_TRANSLATION_FORMAT, $key, $params);

        return Container::camelize($params[1][0]);
    }
}
