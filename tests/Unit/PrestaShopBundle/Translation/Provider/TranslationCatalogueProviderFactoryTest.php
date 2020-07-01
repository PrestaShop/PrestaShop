<?php

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\DefaultCatalogueProvider;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\FileTranslatedCatalogueProvider;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use PrestaShopBundle\Translation\Provider\TranslationCatalogueProviderFactory;
use PrestaShopBundle\Translation\Provider\UserTranslatedCatalogueProvider;

class TranslationCatalogueProviderFactoryTest extends TestCase
{
    /**
     * @var TranslationCatalogueProviderFactory
     */
    private $translationCatalogueProviderFactory;

    public function setUp()
    {
        /** @var MockObject|DatabaseTranslationLoader $databaseLoader */
        $databaseLoader = $this->createMock(DatabaseTranslationLoader::class);
        /** @var MockObject|ThemeProvider $themeProvider */
        $themeProvider = $this->createMock(ThemeProvider::class);
        /** @var MockObject|SearchProvider $searchProvider */
        $searchProvider = $this->createMock(SearchProvider::class);
        /** @var MockObject|ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider */
        $externalModuleLegacySystemProvider = $this->createMock(ExternalModuleLegacySystemProvider::class);

        $this->translationCatalogueProviderFactory = new TranslationCatalogueProviderFactory(
            $databaseLoader,
            $themeProvider,
            $searchProvider,
            $externalModuleLegacySystemProvider,
            'classic',
            '',
            ''
        );
    }

    public function testGetDefaultCatalogueProviderWrongType()
    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessage("The 'type' parameter is not valid. wrong given");
//
//        $this->translationCatalogueProviderFactory->getDefaultCatalogueProvider(
//            'wrong',
//            'classic'
//        );
    }

    public function testGetDefaultCatalogueProvider()
    {
//        $this->assertInstanceOf(
//            ExternalModuleLegacySystemProvider::class,
//            $this->translationCatalogueProviderFactory->getDefaultCatalogueProvider(
//                'external_legacy_module',
//                'classic'
//            )
//        );
//
//        $this->assertInstanceOf(
//            ThemeProvider::class,
//            $this->translationCatalogueProviderFactory->getDefaultCatalogueProvider(
//                'themes',
//                'classic'
//            )
//        );
//
//        foreach (['modules', 'mails', 'mails_body', 'back', 'others'] as $type) {
//            $this->assertInstanceOf(
//                DefaultCatalogueProvider::class,
//                $this->translationCatalogueProviderFactory->getDefaultCatalogueProvider(
//                    $type,
//                    'classic'
//                )
//            );
//        }
    }

    public function testGetFileTranslatedCatalogueProviderWrongType()
    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessage("The 'type' parameter is not valid. wrong given");
//
//        $this->translationCatalogueProviderFactory->getFileTranslatedCatalogueProvider(
//            'wrong',
//            'classic'
//        );
    }

    public function testGetFileTranslatedCatalogueProvider()
    {
//        $this->assertInstanceOf(
//            ExternalModuleLegacySystemProvider::class,
//            $this->translationCatalogueProviderFactory->getFileTranslatedCatalogueProvider(
//                'external_legacy_module',
//                'classic'
//            )
//        );
//
//        $this->assertInstanceOf(
//            ThemeProvider::class,
//            $this->translationCatalogueProviderFactory->getFileTranslatedCatalogueProvider(
//                'themes',
//                'classic'
//            )
//        );
//
//        foreach (['modules', 'mails', 'mails_body', 'back', 'others'] as $type) {
//            $this->assertInstanceOf(
//                FileTranslatedCatalogueProvider::class,
//                $this->translationCatalogueProviderFactory->getFileTranslatedCatalogueProvider(
//                    $type,
//                    'classic'
//                )
//            );
//        }
    }

    public function testGetUserTranslatedCatalogueProviderWrongType()
    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessage("The 'type' parameter is not valid. wrong given");
//
//        $this->translationCatalogueProviderFactory->getUserTranslatedCatalogueProvider(
//            'wrong',
//            'classic'
//        );
    }

    public function testGetUserTranslatedCatalogueProvider()
    {
//        $this->assertInstanceOf(
//            ExternalModuleLegacySystemProvider::class,
//            $this->translationCatalogueProviderFactory->getUserTranslatedCatalogueProvider(
//                'external_legacy_module',
//                'classic'
//            )
//        );
//
//        $this->assertInstanceOf(
//            ThemeProvider::class,
//            $this->translationCatalogueProviderFactory->getUserTranslatedCatalogueProvider(
//                'themes',
//                'classic'
//            )
//        );
//
//        foreach (['modules', 'mails', 'mails_body', 'back', 'others'] as $type) {
//            $this->assertInstanceOf(
//                UserTranslatedCatalogueProvider::class,
//                $this->translationCatalogueProviderFactory->getUserTranslatedCatalogueProvider(
//                    $type,
//                    'classic'
//                )
//            );
//        }
    }

    public function testGetDomainCatalogueProvider()
    {
//        $this->assertInstanceOf(
//            SearchProvider::class,
//            $this->translationCatalogueProviderFactory->getDomainCatalogueProvider(
//                DefaultCatalogueProvider::DEFAULT_LOCALE,
//                '',
//                'classic',
//                ''
//            )
//        );
//        $this->assertInstanceOf(
//            SearchProvider::class,
//            $this->translationCatalogueProviderFactory->getDomainCatalogueProvider(
//                DefaultCatalogueProvider::DEFAULT_LOCALE,
//                '',
//                '',
//                ''
//            )
//        );
//
//        $this->assertInstanceOf(
//            ThemeProvider::class,
//            $this->translationCatalogueProviderFactory->getDomainCatalogueProvider(
//                DefaultCatalogueProvider::DEFAULT_LOCALE,
//                '',
//                'other_theme',
//                ''
//            )
//        );
    }
}
