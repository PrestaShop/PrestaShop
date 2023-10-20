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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ThemeExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsBodyProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ModuleCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ThemeCatalogueLayersProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\LoaderInterface;

class CatalogueProviderFactoryTest extends TestCase
{
    /**
     * @var CatalogueProviderFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $databaseTranslationLoader = $this->createMock(DatabaseTranslationLoader::class);
        $legacyModuleExtractor = $this->createMock(LegacyModuleExtractorInterface::class);
        $legacyFileLoader = $this->createMock(LoaderInterface::class);
        $themeExtractor = $this->createMock(ThemeExtractor::class);
        $themeRepository = $this->createMock(ThemeRepository::class);
        $filesystem = $this->createMock(Filesystem::class);

        $themeRepository
            ->method('getInstanceByName')
            ->willReturn(new Theme([
                'name' => 'classic',
                'directory' => '',
            ])); //doesn't really matter

        $this->factory = new CatalogueProviderFactory(
            $databaseTranslationLoader,
            $legacyModuleExtractor,
            $legacyFileLoader,
            $themeExtractor,
            $themeRepository,
            $filesystem,
            'themesDirectory',
            'modulesDirectory',
            'translationsDirectory'
        );
    }

    public function testGetProviderFailsWhenWrongTypeIsGiven()
    {
        $this->expectException(UnexpectedTranslationTypeException::class);
        $this->factory->getProvider(
            $this->createMock(ProviderDefinitionInterface::class)
        );
    }

    /**
     * @dataProvider getProviderData
     *
     * @throws UnexpectedTranslationTypeException
     */
    public function testGetProvider($providerDefinition, $providerClass): void
    {
        $provider = $this->factory->getProvider($providerDefinition);
        $this->assertInstanceOf($providerClass, $provider);
    }

    public function getProviderData(): iterable
    {
        yield [
            new BackofficeProviderDefinition(),
            CoreCatalogueLayersProvider::class,
        ];
        yield [
            new FrontofficeProviderDefinition(),
            CoreCatalogueLayersProvider::class,
        ];
        yield [
            new MailsProviderDefinition(),
            CoreCatalogueLayersProvider::class,
        ];
        yield [
            new MailsBodyProviderDefinition(),
            CoreCatalogueLayersProvider::class,
        ];
        yield [
            new OthersProviderDefinition(),
            CoreCatalogueLayersProvider::class,
        ];
        yield [
            new ModuleProviderDefinition('module'),
            ModuleCatalogueLayersProvider::class,
        ];
        yield [
            new ThemeProviderDefinition('classic'),
            ThemeCatalogueLayersProvider::class,
        ];
    }
}
