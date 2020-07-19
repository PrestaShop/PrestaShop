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

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider\Strategy;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\BackProvider;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\FrontProvider;
use PrestaShopBundle\Translation\Provider\MailsBodyProvider;
use PrestaShopBundle\Translation\Provider\MailsProvider;
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use PrestaShopBundle\Translation\Provider\OthersProvider;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use PrestaShopBundle\Translation\Provider\ThemeProvider;

class StrategyFactory
{
    /**
     * @var FrontProvider
     */
    private $frontProvider;
    /**
     * @var BackProvider
     */
    private $backProvider;
    /**
     * @var ModulesProvider
     */
    private $modulesProvider;
    /**
     * @var MailsProvider
     */
    private $mailsProvider;
    /**
     * @var MailsBodyProvider
     */
    private $mailsBodyProvider;
    /**
     * @var OthersProvider
     */
    private $othersProvider;
    /**
     * @var ThemeProvider
     */
    private $themeProvider;
    /**
     * @var SearchProvider
     */
    private $searchProvider;
    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory,
        ThemeProvider $themeProvider,
        SearchProvider $searchProvider,
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider
    ) {
        $this->themeProvider = $themeProvider;
        $this->searchProvider = $searchProvider;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;
        $this->frontProvider = new FrontProvider($databaseLoader, $resourceDirectory);
        $this->backProvider = new BackProvider($databaseLoader, $resourceDirectory);
        $this->modulesProvider = new ModulesProvider($databaseLoader, $resourceDirectory);
        $this->mailsProvider = new MailsProvider($databaseLoader, $resourceDirectory);
        $this->mailsBodyProvider = new MailsBodyProvider($databaseLoader, $resourceDirectory);
        $this->othersProvider = new OthersProvider($databaseLoader, $resourceDirectory);
    }

    /**
     * @param string $locale
     * @param string $module
     *
     * @return StrategyInterface
     */
    public function buildExternalLegacyModuleStrategy(string $locale, string $module): StrategyInterface
    {
        return new ExternalLegacyModuleStrategy($this->externalModuleLegacySystemProvider, $locale, $module);
    }

    /**
     * @param string $locale
     * @param string $theme
     *
     * @return StrategyInterface
     */
    public function buildThemesStrategy(string $locale, string $theme): StrategyInterface
    {
        return new ThemesStrategy($this->themeProvider, $locale, $theme);
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param string|null $theme
     * @param string|null $module
     *
     * @return StrategyInterface
     */
    public function buildSearchStrategy(string $locale, string $domain, ?string $theme = null, ?string $module = null): StrategyInterface
    {
        return new SearchStrategy($this->searchProvider, $locale, $domain, $theme, $module);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildFrontStrategy(string $locale): StrategyInterface
    {
        return new FrontStrategy($this->frontProvider, $locale);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildModulesStrategy(string $locale): StrategyInterface
    {
        return new ModulesStrategy($this->modulesProvider, $locale);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildMailsStrategy(string $locale): StrategyInterface
    {
        return new MailsStrategy($this->mailsProvider, $locale);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildMailsBodyStrategy(string $locale): StrategyInterface
    {
        return new MailsBodyStrategy($this->mailsBodyProvider, $locale);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildBackStrategy(string $locale): StrategyInterface
    {
        return new BackStrategy($this->backProvider, $locale);
    }

    /**
     * @param string $locale
     *
     * @return StrategyInterface
     */
    public function buildOthersStrategy(string $locale): StrategyInterface
    {
        return new OthersStrategy($this->othersProvider, $locale);
    }
}
