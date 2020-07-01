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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Translation provider for a specific native module (maintained by the core team)
 * Used mainly for the display in the Translations Manager of the Back Office.
 */
class ModuleProvider implements ProviderInterface
{
    /**
     * @var string Path where translation files are found
     */
    protected $defaultResourceDirectory;
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $defaultResourceDirectory
    ) {
        $this->databaseLoader = $databaseLoader;
        $this->defaultResourceDirectory = $defaultResourceDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'module';
    }

    /**
     * @param string $defaultResourceDirectory
     *
     * @return ModuleProvider
     */
    public function setDefaultResourceDirectory(string $defaultResourceDirectory): ModuleProvider
    {
        $this->defaultResourceDirectory = $defaultResourceDirectory;

        return $this;
    }

    /**
     * @param string $locale
     * @param string $moduleName
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(string $locale, string $moduleName, bool $empty = true): MessageCatalogueInterface
    {
        $resourceDirectory = $this->defaultResourceDirectory . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR;

        return (new DefaultCatalogueProvider(
            $resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters($moduleName)
        ))
            ->getCatalogue($locale, $empty);
    }

    /**
     * @param string $locale
     * @param string $moduleName
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getFileTranslatedCatalogue(string $locale, string $moduleName): MessageCatalogueInterface
    {
        $resourceDirectory = $this->defaultResourceDirectory . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR;

        return (new FileTranslatedCatalogueProvider(
            $resourceDirectory,
            $this->getFilenameFilters($moduleName)
        ))
            ->getCatalogue($locale);
    }

    /**
     * @param string $locale
     * @param string $moduleName
     * @param string|null $theme
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $locale, string $moduleName, ?string $theme = null): MessageCatalogueInterface
    {
        $translationDomains = ['^' . preg_quote(DomainHelper::buildModuleBaseDomain($moduleName)) . '([A-Z]|$)'];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale, $theme);
    }

    /**
     * @param string $moduleName
     *
     * @return string[]
     */
    private function getFilenameFilters(string $moduleName)
    {
        return ['#^' . preg_quote(DomainHelper::buildModuleBaseDomain($moduleName)) . '([A-Z]|\.|$)#'];
    }
}
