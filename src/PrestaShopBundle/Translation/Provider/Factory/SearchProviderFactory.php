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

namespace PrestaShopBundle\Translation\Provider\Factory;

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use PrestaShopBundle\Translation\Provider\ExternalLegacyModuleProvider;
use PrestaShopBundle\Translation\Provider\ProviderInterface;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use PrestaShopBundle\Translation\Provider\Strategy\SearchType;
use PrestaShopBundle\Translation\Provider\Strategy\TypeInterface;

class SearchProviderFactory implements ProviderFactoryInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;
    /**
     * @var string
     */
    private $modulesDirectory;
    /**
     * @var string
     */
    private $translationsDirectory;
    /**
     * @var LegacyFileLoader
     */
    private $legacyFileLoader;
    /**
     * @var LegacyModuleExtractor
     */
    private $legacyModuleExtractor;

    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyFileLoader $legacyFileLoader,
        LegacyModuleExtractor $legacyModuleExtractor,
        string $translationsDirectory,
        string $modulesDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->translationsDirectory = $translationsDirectory;
        $this->modulesDirectory = $modulesDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function implements(TypeInterface $strategy): bool
    {
        return $strategy instanceof SearchType;
    }

    /**
     * {@inheritdoc}
     */
    public function build(TypeInterface $providerType): ProviderInterface
    {
        if (!$this->implements($providerType)) {
            throw new \RuntimeException('Bad strategy given');
        }

        $externalLegacyModuleProvider = null;

        /** @var SearchType $providerType */
        if (!empty($providerType->getModule())) {
            $externalLegacyModuleProvider = new ExternalLegacyModuleProvider(
                $this->databaseTranslationLoader,
                $this->modulesDirectory,
                $this->translationsDirectory,
                $this->legacyFileLoader,
                $this->legacyModuleExtractor,
                $providerType->getModule()
            );
        }

        return new SearchProvider(
            $this->databaseTranslationLoader,
            $this->translationsDirectory,
            $this->modulesDirectory,
            $providerType->getDomain(),
            $externalLegacyModuleProvider,
            $providerType->getModule(),
            $providerType->getTheme()
        );
    }
}
