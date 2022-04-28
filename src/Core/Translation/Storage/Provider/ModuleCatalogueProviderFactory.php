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

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use Symfony\Component\Translation\Loader\LoaderInterface;

class ModuleCatalogueProviderFactory
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    /**
     * @var LegacyModuleExtractorInterface
     */
    private $legacyModuleExtractor;

    /**
     * @var LoaderInterface
     */
    private $legacyFileLoader;

    /**
     * @var string
     */
    private $modulesDirectory;

    /**
     * @var string
     */
    private $translationsDirectory;

    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        LoaderInterface $legacyFileLoader,
        string $modulesDirectory,
        string $translationsDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
    }

    public function getModuleCatalogueProvider(ModuleProviderDefinition $providerDefinition): CatalogueLayersProviderInterface
    {
        return new ModuleCatalogueLayersProvider(
            $this->databaseTranslationLoader,
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDirectory,
            $this->translationsDirectory,
            $providerDefinition->getModuleName(),
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );
    }
}
