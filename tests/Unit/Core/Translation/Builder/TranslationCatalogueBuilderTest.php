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

namespace Tests\Unit\Core\Translation\Builder;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationCatalogueBuilder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueLayersProviderInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\DefaultCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\FileTranslatedCatalogueFinder;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationCatalogueBuilderTest extends TestCase
{
    private const LOCALE = 'fr-FR';

    private static $defaultTranslations = [
        'AdminFirstDomain' => [
            'First Domain First Wording' => 'First Domain First Wording Default Translation',
            'First Domain Second Wording' => 'First Domain Second Wording Default Translation',
        ],
        'AdminSecondDomain' => [
            'Second Domain First Wording' => 'Second Domain First Wording Default Translation',
            'Second Domain Second Wording' => 'Second Domain Second Wording Default Translation',
        ],
        'AdminThirdDomain' => [],
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

    protected function setUp(): void
    {
        $provider = $this->createMock(CatalogueLayersProviderInterface::class);

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

    public function testGetDomainCatalogueFailsWhenDomainIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            '',
            []
        );
    }

    public function testGetDomainCatalogueWithNonExistentDomain(): void
    {
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'SomeFakeDomain',
            []
        );

        $this->assertSame([
            'info' => [
                'locale' => 'en',
                'domain' => 'SomeFakeDomain',
                'theme' => null,
                'total_translations' => 0,
                'total_missing_translations' => 0,
            ],
            'data' => [],
        ], $catalogue);
    }

    /**
     * @dataProvider getDomainCatalogueStructureProvider
     *
     * @param TranslationCatalogueBuilder $translationCatalogueBuilder
     * @param array $parameters
     * @param array $expectedArrayCatalogue
     *
     * @throws Exception
     */
    public function testGetDomainCatalogueStructure(
        TranslationCatalogueBuilder $translationCatalogueBuilder,
        array $parameters,
        array $expectedArrayCatalogue
    ) {
        $catalogue = $translationCatalogueBuilder->getDomainCatalogue(
            $parameters['providerDefinition'],
            $parameters['locale'],
            $parameters['domain'],
            $parameters['search']
        );

        $this->assertSame($expectedArrayCatalogue, $catalogue);
    }

    public function getDomainCatalogueStructureProvider(): array
    {
        return [
            $this->getDomainCatalogueStructureBasicData(),
            $this->getDomainCatalogueStructureRealData(),
        ];
    }

    /**
     * In this test we search for one word and it returns result in one message, one domain.
     */
    public function testGetDomainCatalogueWithOneWordSearch(): void
    {
        // Search single word
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'AdminSecondDomain',
            ['First']
        );

        $this->assertCount(1, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'project' => 'Second Domain First Wording File Translation',
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(1, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);
    }

    /**
     * In this test, we search for multiple words in one term.
     * We also test that if the search doesn't match the message case, it will be found anyway.
     */
    public function testGetDomainCatalogueWithCaseInsensitiveSearchTerms(): void
    {
        // Search multiple words and case insensitive
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'AdminSecondDomain',
            ['fIrst wORDING']
        );

        $this->assertCount(1, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'project' => 'Second Domain First Wording File Translation',
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(1, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);
    }

    /**
     * In this test, we expect that searching a word which match multiple messages will return all the matching messages.
     */
    public function testGetDomainCatalogueWithMultipleResultsSearch(): void
    {
        // Search with multiple results
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'AdminSecondDomain',
            ['Domain']
        );

        $this->assertCount(2, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'project' => null,
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain Second Wording']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'project' => 'Second Domain First Wording File Translation',
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(2, $catalogue['info']['total_translations']);
        $this->assertSame(1, $catalogue['info']['total_missing_translations']);
    }

    /**
     * In this test, we search multiple words. If a message contains any of these words, it will be returned.
     */
    public function testGetDomainCatalogueWithMultipleWordsSearch(): void
    {
        // Search with multiple words
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'AdminSecondDomain',
            [
                'Domain',
                'Second',
            ]
        );

        $this->assertCount(2, $catalogue['data']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'project' => null,
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain Second Wording']);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'project' => 'Second Domain First Wording File Translation',
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $catalogue['data']['Second Domain First Wording']);

        $this->assertSame(2, $catalogue['info']['total_translations']);
        $this->assertSame(1, $catalogue['info']['total_missing_translations']);
    }

    /**
     * In this test, we search a term that exists in no message.
     * Doing this we also test that the words are not taken individually but all the term is search.
     */
    public function testGetDomainCatalogueWithNoResultSearch(): void
    {
        // Search no result
        $catalogue = $this->translationCatalogueBuilder->getDomainCatalogue(
            new BackofficeProviderDefinition(),
            'en',
            'AdminFirstDomain',
            ['Second Domain']
        );

        $this->assertCount(0, $catalogue['data']);
        $this->assertSame([], $catalogue['data']);
        $this->assertSame(0, $catalogue['info']['total_translations']);
        $this->assertSame(0, $catalogue['info']['total_missing_translations']);
    }

    public function testGetCatalogueStructure(): void
    {
        $messages = $this->translationCatalogueBuilder->getCatalogue(
            new BackofficeProviderDefinition(),
            self::LOCALE,
            []
        );
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('AdminFirstDomain', $messages);
        $this->assertArrayHasKey('AdminSecondDomain', $messages);

        $this->assertCount(3, $messages['AdminFirstDomain']);
        $this->assertArrayHasKey(Catalogue::METADATA_KEY_NAME, $messages['AdminFirstDomain']);
        $this->assertArrayHasKey('count', $messages['AdminFirstDomain'][Catalogue::METADATA_KEY_NAME]);
        $this->assertArrayHasKey('missing_translations', $messages['AdminFirstDomain'][Catalogue::METADATA_KEY_NAME]);
        $this->assertArrayHasKey('First Domain First Wording', $messages['AdminFirstDomain']);
        $this->assertArrayHasKey('default', $messages['AdminFirstDomain']['First Domain First Wording']);
        $this->assertArrayHasKey('project', $messages['AdminFirstDomain']['First Domain First Wording']);
        $this->assertArrayHasKey('user', $messages['AdminFirstDomain']['First Domain First Wording']);
    }

    public function testGetCatalogue(): void
    {
        $messages = $this->translationCatalogueBuilder->getCatalogue(
            new BackofficeProviderDefinition(),
            self::LOCALE,
            []
        );
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('AdminFirstDomain', $messages);
        $this->assertArrayHasKey('AdminSecondDomain', $messages);

        $this->assertCount(3, $messages['AdminFirstDomain']);

        $this->assertSame([
            'count' => 2,
            'missing_translations' => 0,
        ], $messages['AdminFirstDomain'][Catalogue::METADATA_KEY_NAME]);

        $this->assertSame([
            'default' => 'First Domain First Wording',
            'project' => 'First Domain First Wording File Translation',
            'user' => 'First Domain First Wording User Translation',
            'tree_domain' => [
                'Admin',
                'First',
                'Domain',
            ],
        ], $messages['AdminFirstDomain']['First Domain First Wording']);

        $this->assertSame([
            'count' => 2,
            'missing_translations' => 1,
        ], $messages['AdminSecondDomain'][Catalogue::METADATA_KEY_NAME]);

        $this->assertSame([
            'default' => 'Second Domain First Wording',
            'project' => 'Second Domain First Wording File Translation',
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $messages['AdminSecondDomain']['Second Domain First Wording']);

        $this->assertSame([
            'default' => 'Second Domain Second Wording',
            'project' => null,
            'user' => null,
            'tree_domain' => [
                'Admin',
                'Second',
                'Domain',
            ],
        ], $messages['AdminSecondDomain']['Second Domain Second Wording']);
    }

    private function buildCatalogueProviderFromCatalogues(
        array $defaultTranslations,
        array $fileTranslatedTranslations,
        array $userTranslatedTranslations
    ): TranslationCatalogueBuilder {
        $provider = $this->createMock(CatalogueLayersProviderInterface::class);

        // Build Default catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach ($defaultTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getDefaultCatalogue')->willReturn($catalogue);

        // Build FileTranslated catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach ($fileTranslatedTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getFileTranslatedCatalogue')->willReturn($catalogue);

        // Build UserTranslated catalogue
        $catalogue = new MessageCatalogue(self::LOCALE);
        foreach ($userTranslatedTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        $provider->method('getUserTranslatedCatalogue')->willReturn($catalogue);

        // The factory
        $providerFactory = $this->createMock(CatalogueProviderFactory::class);
        $providerFactory->method('getProvider')->willReturn($provider);

        return new TranslationCatalogueBuilder($providerFactory);
    }

    private function getDomainCatalogueStructureBasicData(): array
    {
        $translationCatalogueBuilder = $this->buildCatalogueProviderFromCatalogues(
            self::$defaultTranslations,
            self::$fileTranslatedTranslations,
            self::$userTranslatedTranslations
        );

        return [
            $translationCatalogueBuilder,
            [
                'providerDefinition' => new BackofficeProviderDefinition(),
                'locale' => 'en',
                'domain' => 'AdminSecondDomain',
                'search' => [],
            ],
            [
                'info' => [
                    'locale' => 'en',
                    'domain' => 'AdminSecondDomain',
                    'theme' => null,
                    'total_translations' => 2,
                    'total_missing_translations' => 1,
                ],
                'data' => [
                    'Second Domain Second Wording' => [
                        'default' => 'Second Domain Second Wording',
                        'project' => null,
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Second',
                            'Domain',
                        ],
                    ],
                    'Second Domain First Wording' => [
                        'default' => 'Second Domain First Wording',
                        'project' => 'Second Domain First Wording File Translation',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Second',
                            'Domain',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getDomainCatalogueStructureRealData(): array
    {
        $translationsCatalogueDirectory = __DIR__ . '/../Resources/';
        $defaultTranslations = (new DefaultCatalogueFinder($translationsCatalogueDirectory, ['#^Admin[A-Z]#']))
            ->getCatalogue(self::LOCALE)->all();

        $fileTranslatedTranslations = (new FileTranslatedCatalogueFinder($translationsCatalogueDirectory, ['#^Admin[A-Z]#']))
            ->getCatalogue(self::LOCALE)->all();

        $translationCatalogueBuilder = $this->buildCatalogueProviderFromCatalogues(
            $defaultTranslations,
            $fileTranslatedTranslations,
            []
        );

        return [
            $translationCatalogueBuilder,
            [
                'providerDefinition' => new BackofficeProviderDefinition(),
                'locale' => 'en',
                'domain' => 'AdminCatalogFeature',
                'search' => ['Delivery'],
            ],
            [
                'info' => [
                    'locale' => 'en',
                    'domain' => 'AdminCatalogFeature',
                    'theme' => null,
                    'total_translations' => 5,
                    'total_missing_translations' => 0,
                ],
                'data' => [
                    'Delivery time of in-stock products:' => [
                        'default' => 'Delivery time of in-stock products:',
                        'project' => 'Délai de livraison pour les produits en stock :',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Catalog',
                            'Feature',
                        ],
                    ],
                    'Default delivery time' => [
                        'default' => 'Default delivery time',
                        'project' => 'Délai de livraison par défaut',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Catalog',
                            'Feature',
                        ],
                    ],
                    'Specific delivery time to this product' => [
                        'default' => 'Specific delivery time to this product',
                        'project' => 'Délai de livraison spécifique pour ce produit',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Catalog',
                            'Feature',
                        ],
                    ],
                    'Delivery Time' => [
                        'default' => 'Delivery Time',
                        'project' => 'Delai de livraison',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Catalog',
                            'Feature',
                        ],
                    ],
                    'Delivery time of out-of-stock products with allowed orders:' => [
                        'default' => 'Delivery time of out-of-stock products with allowed orders:',
                        'project' => 'Délai de livraison des produits épuisés avec commande autorisée:',
                        'user' => null,
                        'tree_domain' => [
                            'Admin',
                            'Catalog',
                            'Feature',
                        ],
                    ],
                ],
            ],
        ];
    }
}
