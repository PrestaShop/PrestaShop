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

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\Catalogue\TranslationCatalogueProviderInterface;
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class ModulesProviderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    private static $wordings = [
        'ModulesModulenameSomethingElse' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
        'ModulesModulenameSomeDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
        ],
    ];

    private static $emptyWordings = [
        'ModulesModulenameSomethingElse' => [
            'Foo' => '',
            'Bar' => '',
        ],
        'ModulesModulenameSomeDomain' => [
            'Some wording' => '',
            'Some other wording' => '',
        ],
    ];

    /**
     * @var ModulesProvider
     */
    private $externalModuleLegacySystemProvider;

    public function setUp()
    {
        /** @var MockObject|DatabaseTranslationLoader $databaseLoader */
        $databaseLoader = $this->createMock(DatabaseTranslationLoader::class);
        /** @var MockObject|LoaderInterface $legacyFileLoader */
        $legacyFileLoader = $this->createMock(LoaderInterface::class);

        $catalogue = new MessageCatalogue(TranslationCatalogueProviderInterface::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        /** @var MockObject|LegacyModuleExtractorInterface $legacyModuleExtractor */
        $legacyModuleExtractor = $this->createMock(LegacyModuleExtractorInterface::class);
        $legacyModuleExtractor
            ->method('extract')
            ->willReturn($catalogue);

        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ExternalModuleProviderProviderTest']);
        if (!is_dir(self::$tempDir)) {
            mkdir(self::$tempDir);
        }

        $this->externalModuleLegacySystemProvider = (new ModulesProvider(
            $databaseLoader,
            self::$tempDir,
            self::$tempDir,
            $legacyFileLoader,
            $legacyModuleExtractor,
            'moduleName'
        ));
    }

    public static function setUpBeforeClass()
    {
        $catalogue = new MessageCatalogue(TranslationCatalogueProviderInterface::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }

        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ExternalModuleProviderProviderTest']);
        if (!is_dir(self::$tempDir)) {
            mkdir(self::$tempDir);
        }
        if (!is_dir(self::$tempDir . DIRECTORY_SEPARATOR . TranslationCatalogueProviderInterface::DEFAULT_LOCALE)) {
            mkdir(self::$tempDir . DIRECTORY_SEPARATOR . TranslationCatalogueProviderInterface::DEFAULT_LOCALE);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => implode(DIRECTORY_SEPARATOR, [self::$tempDir, 'moduleName', 'translations']) . DIRECTORY_SEPARATOR,
        ]);
        (new XliffFileDumper())->dump($catalogue, [
            'path' => implode(
                    DIRECTORY_SEPARATOR,
                    [self::$tempDir, 'moduleName', 'translations', TranslationCatalogueProviderInterface::DEFAULT_LOCALE]
                ) . DIRECTORY_SEPARATOR,
        ]);
    }

    public function testGetDefaultCatalogue()
    {
        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue(
            TranslationCatalogueProviderInterface::DEFAULT_LOCALE
        )->all();

        $this->assertSame(self::$wordings, $catalogue);

        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue(
            TranslationCatalogueProviderInterface::DEFAULT_LOCALE,
            true
        )->all();

        $this->assertSame(self::$wordings, $catalogue);

        $catalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue(
            'fr-FR',
            true
        )->all();

        $catalogueFr = self::$emptyWordings;
        foreach (array_keys($catalogueFr) as $translationKey) {
            $translationKeyLocale = sprintf('%s.%s', $translationKey, TranslationCatalogueProviderInterface::DEFAULT_LOCALE);
            $catalogueFr[$translationKeyLocale] = $catalogueFr[$translationKey];
            unset($catalogueFr[$translationKey]);
        }
        $this->assertSame($catalogueFr, $catalogue);
    }

    public function testGetFilesystemCatalogue()
    {
        $catalogue = $this->externalModuleLegacySystemProvider->getFileTranslatedCatalogue(
            TranslationCatalogueProviderInterface::DEFAULT_LOCALE
        )->all();

        $this->assertSame(self::$wordings, $catalogue);
    }
}
