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

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use LogicException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;

class TranslationCatalogueProviderFactory
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string
     */
    private $resourceDirectory;

    /**
     * @var string
     */
    private $themeResourceDirectory;

    /**
     * @var ThemeProvider
     */
    private $themeProvider;

    /**
     * @var SearchProvider
     */
    private $searchProvider;

    /**
     * @var string
     */
    private $defaultTheme;

    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        ThemeProvider $themeProvider,
        SearchProvider $searchProvider,
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider,
        string $defaultTheme,
        string $resourceDirectory,
        string $themeResourceDirectory
    ) {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->themeResourceDirectory = $themeResourceDirectory;
        $this->themeProvider = $themeProvider;
        $this->searchProvider = $searchProvider;
        $this->defaultTheme = $defaultTheme;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param string|null $theme
     *
     * @return DefaultCatalogueProviderInterface
     */
    public function getDefaultCatalogueProvider(
        string $type,
        string $locale,
        ?string $theme = null
    ): DefaultCatalogueProviderInterface {
        if (!in_array($type, ['front', 'modules', 'themes', 'mails', 'mails_body', 'back', 'others', 'external_legacy_module'])) {
            throw new LogicException("The 'type' parameter is not valid. $type given");
        }

        if ('external_legacy_module' === $type) {
            return $this->getExternalLegacyModuleProvider($locale);
        }
        if ('themes' === $type) {
            return $this->getThemeCatalogueProvider($locale, $theme);
        }

        return new DefaultCatalogueProvider(
            $locale,
            $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters($type)
        );
    }

    /**
     * @param string $type
     * @param string|null $locale
     * @param string|null $theme
     *
     * @return FileTranslatedCatalogueProviderInterface
     */
    public function getFileTranslatedCatalogueProvider(
        string $type,
        string $locale,
        ?string $theme
    ): FileTranslatedCatalogueProviderInterface {
        if (!in_array($type, ['front', 'modules', 'themes', 'mails', 'mails_body', 'back', 'others', 'external_legacy_module'])) {
            throw new LogicException("The 'type' parameter is not valid. $type given");
        }
        if ('external_legacy_module' === $type) {
            return $this->getExternalLegacyModuleProvider($locale);
        }
        if ('themes' === $type) {
            return $this->getThemeCatalogueProvider($locale, $theme);
        }

        return new FileTranslatedCatalogueProvider(
            $locale,
            $this->resourceDirectory,
            $this->getFilenameFilters($type)
        );
    }

    /**
     * @param string $type
     * @param string|null $locale
     * @param string|null $theme
     *
     * @return UserTranslatedCatalogueProviderInterface
     */
    public function getUserTranslatedCatalogueProvider(
        string $type,
        string $locale,
        ?string $theme
    ): UserTranslatedCatalogueProviderInterface {
        if (!in_array($type, ['front', 'modules', 'themes', 'mails', 'mails_body', 'back', 'others', 'external_legacy_module'])) {
            throw new LogicException("The 'type' parameter is not valid. $type given");
        }

        if ('external_legacy_module' === $type) {
            return $this->getExternalLegacyModuleProvider($locale);
        }
        if ('themes' === $type) {
            return $this->getThemeCatalogueProvider($locale, $theme);
        }

        return new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $locale,
            $this->getTranslationDomains($type)
        );
    }

    /**
     * @param string $locale
     *
     * @return ExternalModuleLegacySystemProvider
     */
    public function getExternalLegacyModuleProvider(string $locale): ExternalModuleLegacySystemProvider
    {
        return $this->externalModuleLegacySystemProvider->setLocale($locale);
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param string|null $theme
     * @param string|null $module
     *
     * @return ProviderInterface
     */
    public function getDomainCatalogueProvider(
        string $locale,
        string $domain,
        ?string $theme,
        ?string $module
    ): ProviderInterface {
        if (!empty($theme) && $this->defaultTheme !== $theme) {
            return $this->getThemeCatalogueProvider(
                $locale,
                $theme,
                $domain
            );
        } else {
            return $this->getSearchCatalogueProvider(
                $locale,
                $domain,
                $module
            );
        }
    }

    /**
     * @param string $locale
     * @param string $theme
     * @param string|null $domain
     *
     * @return ThemeProvider
     */
    public function getThemeCatalogueProvider(string $locale, string $theme, ?string $domain = null): ThemeProvider
    {
        $provider = $this->themeProvider
            ->setLocale($locale)
            ->setThemeName($theme);

        if (null !== $domain) {
            $provider->setDomain($domain);
        }

        return $provider;
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param string|null $module
     *
     * @return SearchProvider
     */
    private function getSearchCatalogueProvider(string $locale, string $domain, ?string $module): SearchProvider
    {
        $provider = $this->searchProvider
            ->setLocale($locale)
            ->setDomain($domain);

        if (null !== $module) {
            $provider->setModuleName($module);
        }

        return $provider;
    }

    /**
     * @param string $type
     *
     * @return array|string[]
     */
    private function getFilenameFilters(string $type): array
    {
        $filenameFilters = [];

        switch ($type) {
            case 'back':
                $filenameFilters = [
                    '#^Admin[A-Z]#',
                    '#^Modules[A-Z](.*)Admin#',
                ];
                break;
            case 'front':
                $filenameFilters = [
                    '#^Shop*#',
                    '#^Modules(.*)Shop#',
                ];
                break;
            case 'modules':
                $filenameFilters = ['#^Modules[A-Z]#'];
                break;
            case 'mails':
                $filenameFilters = ['#EmailsSubject*#'];
                break;
            case 'mails_body':
                $filenameFilters = ['#EmailsBody*#'];
                break;
            case 'others':
                $filenameFilters = ['#^messages*#'];
                break;
        }

        return $filenameFilters;
    }

    /**
     * @param string $type
     *
     * @return array|string[]
     */
    private function getTranslationDomains(string $type): array
    {
        $translationDomains = [];

        switch ($type) {
            case 'back':
                $translationDomains = [
                    '^Admin[A-Z]',
                    '^Modules[A-Z](.*)Admin',
                ];
                break;
            case 'front':
                $translationDomains = [
                    '^Shop*',
                    '^Modules(.*)Shop',
                ];
                break;
            case 'modules':
                $translationDomains = ['^Modules[A-Z]'];
                break;
            case 'mails':
                $translationDomains = ['EmailsSubject*'];
                break;
            case 'mails_body':
                $translationDomains = ['EmailsBody*'];
                break;
            case 'others':
                $translationDomains = ['^messages*'];
                break;
        }

        return $translationDomains;
    }
}
