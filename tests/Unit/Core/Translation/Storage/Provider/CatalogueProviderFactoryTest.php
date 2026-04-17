<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                'name' => Theme::getDefaultTheme(),
                'directory' => '',
            ])); // doesn't really matter

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
            new ThemeProviderDefinition(Theme::getDefaultTheme()),
            ThemeCatalogueLayersProvider::class,
        ];
    }
}
