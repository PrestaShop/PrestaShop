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

namespace Tests\Unit\Core\Translation\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationCatalogueBuilder;
use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationsTreeBuilder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueLayersProviderInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationTreeBuilderTest extends TestCase
{
    private const LOCALE = 'en-US';
    private const FRENCH_LOCALE = 'fr-FR';

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
     * @var TranslationsTreeBuilder
     */
    private $treeBuilder;

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

        $router = $this->createMock(Router::class);
        $router->method('generate')->willReturn('route');

        $this->treeBuilder = new TranslationsTreeBuilder(
            $router,
            new TranslationCatalogueBuilder($providerFactory)
        );
    }

    public function testGetTreeStructure(): void
    {
        $tree = $this->treeBuilder->getTree(
            new BackofficeProviderDefinition(),
            self::LOCALE,
            []
        );

        $this->assertArrayHasKey('tree', $tree);
        $this->assertArrayHasKey('total_translations', $tree['tree']);
        $this->assertArrayHasKey('total_missing_translations', $tree['tree']);
        $this->assertArrayHasKey('children', $tree['tree']);
        $this->assertCount(1, $tree['tree']['children']);
        $this->assertSame([
            'name',
            'full_name',
            'domain_catalog_link',
            'total_translations',
            'total_missing_translations',
            'children',
        ], array_keys($tree['tree']['children'][0]));

        $this->assertCount(2, $tree['tree']['children'][0]['children']);
        $this->assertSame([
            'name',
            'full_name',
            'domain_catalog_link',
            'total_translations',
            'total_missing_translations',
            'children',
        ], array_keys($tree['tree']['children'][0]['children'][0]));
    }

    public function testGetTreeContent(): void
    {
        $tree = $this->treeBuilder->getTree(
            new BackofficeProviderDefinition(),
            self::FRENCH_LOCALE,
            []
        );

        $this->assertArrayHasKey('tree', $tree);
        $this->assertSame(4, $tree['tree']['total_translations']);
        $this->assertSame(1, $tree['tree']['total_missing_translations']);

        $this->assertSame('Admin', $tree['tree']['children'][0]['name']);
        $this->assertSame('Admin', $tree['tree']['children'][0]['full_name']);
        $this->assertSame('route', $tree['tree']['children'][0]['domain_catalog_link']);
        $this->assertSame(4, $tree['tree']['children'][0]['total_translations']);
        $this->assertSame(1, $tree['tree']['children'][0]['total_missing_translations']);

        $this->assertCount(2, $tree['tree']['children'][0]['children']);

        $this->assertSame('First', $tree['tree']['children'][0]['children'][0]['name']);
        $this->assertSame('AdminFirst', $tree['tree']['children'][0]['children'][0]['full_name']);
        $this->assertSame('route', $tree['tree']['children'][0]['children'][0]['domain_catalog_link']);
        $this->assertSame(2, $tree['tree']['children'][0]['children'][0]['total_translations']);
        $this->assertSame(0, $tree['tree']['children'][0]['children'][0]['total_missing_translations']);

        $this->assertSame('Second', $tree['tree']['children'][0]['children'][1]['name']);
        $this->assertSame('AdminSecond', $tree['tree']['children'][0]['children'][1]['full_name']);
        $this->assertSame('route', $tree['tree']['children'][0]['children'][1]['domain_catalog_link']);
        $this->assertSame(2, $tree['tree']['children'][0]['children'][1]['total_translations']);
        $this->assertSame(1, $tree['tree']['children'][0]['children'][1]['total_missing_translations']);
    }
}
