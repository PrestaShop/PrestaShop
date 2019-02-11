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

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

final class LegacyFileExtractor implements LegacyFileExtractorInterface
{
    /**
     * @param string $path
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function extract($path, $locale)
    {
        $_MODULE = [];
        $locale = $this->convertToCatalogueLocale($locale);
        $filepath = $path . "$locale.php";

        if (!file_exists($filepath)) {
            throw new \Exception('There is no translation file available.');
        }

        include_once $filepath;
        $catalogue = new MessageCatalogue($locale);

        foreach ($_MODULE as $translationKey => $translationValue) {
            $domain = $this->getDomain($translationKey);
            $id = $this->getId($translationKey);
            $catalogue->set($id, $translationValue, $domain);
        }

        return $catalogue;
    }

    private function convertToCatalogueLocale($locale)
    {
        return substr($locale, 0, 2);
    }

    private function getId($key)
    {
        $regexp = "#\<\{([\w-]+)\}prestashop\>([\w-]+)_([\w-]+)#";
        preg_match_all($regexp, $key, $params);

        return Container::camelize($params[3][0]);
    }

    private function getDomain($key)
    {
        $regexp = "#\<\{([\w-]+)\}prestashop\>([\w-]+)_([\w-]+)#";
        preg_match_all($regexp, $key, $params);

        return Container::camelize($params[1][0]);
    }
}
