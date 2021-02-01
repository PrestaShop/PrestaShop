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

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Provider\CatalogueProviderFactory;
use PrestaShopBundle\Translation\Provider\CatalogueProviderInterface;
use PrestaShopBundle\Translation\TranslationCatalogueBuilder;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationCatalogueBuilderTest extends TestCase
{
    private const LOCALE = 'en-US';

    private static $defaultTranslations = [
        'AdminFirstDomain' => [
            'First Domain First Wording' => 'First Domain First Wording Default Translation',
            'First Domain Second Wording' => 'First Domain Second Wording Default Translation',
        ],
        'AdminSecondDomain' => [
            'Second Domain First Wording' => 'Second Domain First Wording Default Translation',
            'Second Domain Second Wording' => 'Second Domain Second Wording Default Translation',
        ],
    ];
    private static $fileTranslatedTranslations = [
        'AdminFirstDomain' => [
            'First Domain First Wording' => 'First Domain First Wording File Translation',
            'First Domain Second Wording' => 'First Domain Second Wording File Translation',
        ],
        'AdminSecondDomain' => [
            'Second Domain First Wording' => 'Second Domain First Wording File Translation',
        ],
    ];
    private static $userTranslatedTranslations = [
        'AdminFirstDomain' => [
            'First Domain First Wording' => 'First Domain First Wording User Translation',
            'First Domain Second Wording' => 'First Domain Second Wording User Translation',
        ],
    ];

    /**
     * @var TranslationCatalogueBuilder
     */
    private $translationCatalogueBuilder;

    public function setUp()
    {
        $provider = $this->createMock(CatalogueProviderInterface::class);

        // Build Default catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach (self::$defaultTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getDefaultCatalogue')->willReturn($catalogue);

        // Build FileTranslated catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach (self::$fileTranslatedTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getFileTranslatedCatalogue')->willReturn($catalogue);

        // Build UserTranslated catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach (self::$userTranslatedTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getUserTranslatedCatalogue')->willReturn($catalogue);

        // The factory
        $providerFactory = $this->createMock(CatalogueProviderFactory::class);
        $providerFactory->method('getProvider')->willReturn($provider);

        $this->translationCatalogueBuilder = new TranslationCatalogueBuilder($providerFactory);
    }

    public function testGetDomainCatalogueWrongType()
    {
        $this->expectException(UnexpectedTranslationTypeException::class);
        $this->translationCatalogueBuilder->getDomainCatalogue(
            'toto',
            'en',
            'domain',
            [],
            'theme',
            'module'
        );
    }

    public function testGetDomainCatalogueEmptyTheme()
    {
        $this->expectException(\Exception::class);
        $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_THEMES,
            'en',
            'domain',
            [],
            '',
            'module'
        );
    }

    public function testGetDomainCatalogueEmptyModule()
    {
        $this->expectException(\Exception::class);
        $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_MODULES,
            'en',
            'domain',
            [],
            'theme',
            ''
        );
    }

    public function testGetDomainCatalogueStructure()
    {
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminSecondDomain',
            [],
            'theme',
            'module'
        );

        $this->assertArrayHasKey('data', $catalogue);
        $this->assertArrayHasKey('info', $catalogue);

        $this->assertCount(2, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'xliff' => null,
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain Second Wording']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertArrayHasKey('locale', $catalogue['info']);
        $this->assertArrayHasKey('domain', $catalogue['info']);
        $this->assertArrayHasKey('theme', $catalogue['info']);
        $this->assertArrayHasKey('total_translations', $catalogue['info']);
        $this->assertSame(2, $catalogue['info']['total_translations']);
        $this->assertArrayHasKey('total_missing_translations', $catalogue['info']);
        $this->assertSame(1, $catalogue['info']['total_missing_translations']);
    }

    public function testGetDomainCatalogueSearch()
    {
        // Search single word
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminSecondDomain',
            ['First'],
            'theme',
            'module'
        );

        $this->assertCount(1, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(1, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);

        // Search multiple words and case insensitive
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminSecondDomain',
            ['fIrst wORDING'],
            'theme',
            'module'
        );

        $this->assertCount(1, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(1, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);

        // Search with multiple results
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminSecondDomain',
            ['Domain'],
            'theme',
            'module'
        );

        $this->assertCount(2, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'xliff' => null,
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain Second Wording']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(2, $catalogue['info']['total_translations']);
        $this->assertSame(1, $catalogue['info']['total_missing_translations']);

        // Search with multiple words
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminSecondDomain',
            [
                'Domain',
                'Second',
            ],
            'theme',
            'module'
        );

        $this->assertCount(2, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'xliff' => null,
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain Second Wording']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(2, $catalogue['info']['total_translations']);
        $this->assertSame(1, $catalogue['info']['total_missing_translations']);

        // Search no result
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            'en',
            'AdminFirstDomain',
            ['Second Domain'],
            'theme',
            'module'
        );

        $this->assertCount(0, $catalogue['data']);
        $this->assertSame([], $catalogue['data']);
        $this->assertSame(0, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);
    }

    public function testGetCatalogueStructure()
    {
        $messages = $this->translationCatalogueBuilder->getCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            self::LOCALE,
            [],
            'theme',
            'module'
        );
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('AdminFirstDomain', $messages);
        $this->assertArrayHasKey('AdminSecondDomain', $messages);

        $this->assertCount(3, $messages['AdminFirstDomain']);
        $this->assertArrayHasKey('__metadata', $messages['AdminFirstDomain']);
        $this->assertArrayHasKey('count', $messages['AdminFirstDomain']['__metadata']);
        $this->assertArrayHasKey('missing_translations', $messages['AdminFirstDomain']['__metadata']);
        $this->assertArrayHasKey('First Domain First Wording', $messages['AdminFirstDomain']);
        $this->assertArrayHasKey('default', $messages['AdminFirstDomain']['First Domain First Wording']);
        $this->assertArrayHasKey('xliff', $messages['AdminFirstDomain']['First Domain First Wording']);
        $this->assertArrayHasKey('database', $messages['AdminFirstDomain']['First Domain First Wording']);
    }

    public function testGetCatalogue()
    {
        $messages = $this->translationCatalogueBuilder->getCatalogue(
            TranslationCatalogueBuilder::TYPE_BACK,
            self::LOCALE,
            [],
            'theme',
            'module'
        );
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('AdminFirstDomain', $messages);
        $this->assertArrayHasKey('AdminSecondDomain', $messages);

        $this->assertCount(3, $messages['AdminFirstDomain']);

        $this->assertSame([
            'count' => 2,
            'missing_translations' => 0,
        ], $messages['AdminFirstDomain']['__metadata']);

        $this->assertSame([
            'default' => 'First Domain First Wording',
            'xliff' => 'First Domain First Wording File Translation',
            'database' => 'First Domain First Wording User Translation',
            'tree_domain' => [
                'Admin',
                'First',
                'Domain',
            ],
        ], $messages['AdminFirstDomain']['First Domain First Wording']);

        $this->assertSame([
            'count' => 2,
            'missing_translations' => 1,
        ], $messages['AdminSecondDomain']['__metadata']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'xliff' => 'Second Domain First Wording File Translation',
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $messages['AdminSecondDomain']['Second Domain First Wording']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'xliff' => null,
            'database' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $messages['AdminSecondDomain']['Second Domain Second Wording']);
    }
}
