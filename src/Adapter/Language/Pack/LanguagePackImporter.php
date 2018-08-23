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

namespace PrestaShop\PrestaShop\Adapter\Language\Pack;

use Exception;
use PrestaShop\PrestaShop\Adapter\Cache\CacheClearer;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\Language\LanguagePackInstaller;
use PrestaShop\PrestaShop\Core\Cldr\Update;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\LanguagePackImporterInterface;
use PrestaShop\PrestaShop\Core\Language\Pack\LanguagePackInstallerInterface;

/**
 * Class LanguagePackImporter is responsible for importing language pack
 */
final class LanguagePackImporter implements LanguagePackImporterInterface
{
    /**
     * @var LanguagePackInstaller
     */
    private $languagePack;
    /**
     * @var LanguageDataProvider
     */
    private $languageProvider;
    /**
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @param LanguagePackInstallerInterface $languagePack
     * @param LanguageDataProvider $languageProvider
     * @param CacheClearer $cacheClearer
     */
    public function __construct(
        LanguagePackInstallerInterface $languagePack,
        LanguageDataProvider $languageProvider,
        CacheClearer $cacheClearer
    ) {
        $this->languagePack = $languagePack;
        $this->languageProvider = $languageProvider;
        $this->cacheClearer = $cacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function import($isoCode)
    {
        $result = $this->languagePack->downloadAndInstallLanguagePack($isoCode);

        if (!empty($result)) {
            return $result;
        }

        $this->updateCldr($isoCode);

        return [];
    }

    /**
     * Fetches CLDR data for currently updated or added language
     *
     * @param $isoCode
     *
     * @throws Exception
     */
    private function updateCldr($isoCode)
    {
        $this->cacheClearer->clearAllCaches();

        $languageCode = $this->languageProvider->getLanguageCodeByIso($isoCode);
        $languageCode = $this->getFormattedLanguageCode($languageCode);

        $cldrUpdate = new Update(_PS_TRANSLATIONS_DIR_);
        $cldrUpdate->fetchLocale($languageCode);
    }

    /**
     * Gets language code in format of ISO 639-1
     *
     * @param string $languageCode - language code to format
     *
     * @return string
     */
    private function getFormattedLanguageCode($languageCode)
    {
        $explodedLangCode = explode('-', $languageCode);

        return sprintf(
            '%s-%s',
            isset($explodedLangCode[0]) ? $explodedLangCode[0] : '',
            isset($explodedLangCode[1]) ? strtoupper($explodedLangCode[1]) : ''
        );
    }
}
