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

use Symfony\Component\Translation\Loader\LoaderInterface;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Exception\LegacyFileFormattingException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Util\ModuleDomainConverterInterface;

/**
 * Able to convert old translation files (in translations/<shopLocale>.php) into
 * a valid Symfony MessageCatalogue.
 */
final class LegacyFileTranslationLoader implements LoaderInterface
{
    /**
     * @var string the expected format of a legacy translation key
     */
    const LEGACY_TRANSLATION_FORMAT = '#\<\{(?<module>[\w-]+)\}prestashop\>(?<domain>[\w-]+)_(?<id>[\w-]+)#';

    /**
     * @var Converter the locale Converter
     */
    private $localeConverter;

    /**
     * @var ModuleDomainConverterInterface the module domain converter
     */
    private $moduleDomainConverter;

    /**
     * @var LegacyModuleExtractorInterface the module extractor able to parse all files of a module
     *                                     to retrieve the default keys
     */
    private $moduleExtractor;

    public function __construct(
        Converter $converter,
        LegacyModuleExtractorInterface $moduleExtractor,
        ModuleDomainConverterInterface $moduleDomainConverter
    ) {
        $this->localeConverter = $converter;
        $this->moduleExtractor = $moduleExtractor;
        $this->moduleDomainConverter = $moduleDomainConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function load($path, $locale, $domain = 'messages')
    {
        // Each legacy file declare this variable to store the translations
        $_MODULE = [];

        if (!file_exists($path)) {
            throw UnsupportedLocaleException::fileNotFound($path, $locale);
        }

        // get module name from Domain (needs to be extracted)
        $catalogue = $this->moduleExtractor->extract(
            $this->moduleDomainConverter->getModuleFromDomain($domain),
            $locale
        );

        // Load a global array $_MODULE
        include_once $path;

        $legacyFilesTranslations = [];

        foreach ($_MODULE as $translationKey => $translationValue) {
            $id = $this->getId($translationKey);
            $legacyFilesTranslations[$id] = $translationValue;
        }

        foreach ($catalogue->all($domain) as $translationKey => $defaultValue) {
            foreach ($legacyFilesTranslations as $md5Key => $localizedValue) {
                if (md5($translationKey) === $md5Key) {
                    $catalogue->set($translationKey, $localizedValue, $domain);

                    break 1;
                }
            }
        }

        $catalogue->replace([], 'messages');

        return $catalogue;
    }

    /**
     * @param string $key the translation key
     *
     * @return string the translation id
     */
    private function getId($key)
    {
        preg_match_all(self::LEGACY_TRANSLATION_FORMAT, $key, $matches);
        if (empty($matches['id'][0])) {
            throw LegacyFileFormattingException::fileIsInvalid();
        }

        return $matches['id'][0];
    }
}
