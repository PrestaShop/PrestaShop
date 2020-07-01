<?php

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use PrestaShopBundle\Translation\Provider\DefaultCatalogueProvider;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\ModuleProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class ExternalModuleLegacySystemProviderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    private static $wordings = [
        'ShopSomeDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
        ],
        'ShopSomethingElse' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
    ];

    private static $emptyWordings = [
        'ShopSomeDomain' => [
            'Some wording' => '',
            'Some other wording' => '',
        ],
        'ShopSomethingElse' => [
            'Foo' => '',
            'Bar' => '',
        ],
    ];

    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function setUp()
    {
        /** @var MockObject|DatabaseTranslationLoader $databaseLoader */
        $databaseLoader = $this->createMock(DatabaseTranslationLoader::class);
        /** @var MockObject|LoaderInterface $legacyFileLoader */
        $legacyFileLoader = $this->createMock(LoaderInterface::class);

        $catalogue = new MessageCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        /** @var MockObject|LegacyModuleExtractorInterface $legacyModuleExtractor */
        $legacyModuleExtractor = $this->createMock(LegacyModuleExtractorInterface::class);
        $legacyModuleExtractor
            ->method('extract')
            ->willReturn($catalogue);
        /** @var MockObject|ModuleProvider $moduleProvider */
        $moduleProvider = $this->createMock(ModuleProvider::class);
        $moduleProvider
            ->method('setModuleName')
            ->willReturn($moduleProvider);

        $this->externalModuleLegacySystemProvider = (new ExternalModuleLegacySystemProvider(
            $databaseLoader,
            '',
            $legacyFileLoader,
            $legacyModuleExtractor,
            $moduleProvider
        ));
    }

    public static function setUpBeforeClass()
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ExternalModuleProviderProviderTest']);

        $catalogue = new MessageCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir,
        ]);
    }

    public function testGetDefaultCatalogue()
    {
        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        $this->assertSame(self::$wordings, $catalogue->all());

        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE, true);
        $this->assertSame(self::$wordings, $catalogue->all());

        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue('fr-FR', true);
        $this->assertSame(self::$emptyWordings, $catalogue->all());
    }

    public function xtestGetFilesystemCatalogue()
    {
        $catalogue = $this->externalModuleLegacySystemProvider->getFileTranslatedCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        $this->assertSame(self::$wordings, $catalogue->all());
    }
}
