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

namespace Tests\Unit\PrestaShopBundle\Translation\Provider\Catalogue;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\Catalogue\DefaultCatalogueProvider;
use PrestaShopBundle\Translation\Provider\Catalogue\FileTranslatedCatalogueProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class FileTranslatedCatalogueProviderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    public static function setUpBeforeClass()
    {
        self::$tempDir = implode(
            DIRECTORY_SEPARATOR,
            [sys_get_temp_dir(), 'FileTranslatedCatalogueProviderTest']
        );
        $wordings = [
            'ShopSomeDomain' => [
                'Some wording' => 'Some wording',
                'Some other wording' => 'Some other wording',
            ],
            'ShopSomethingElse' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
            ],
        ];
        $catalogue = new MessageCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        foreach ($wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . DefaultCatalogueProvider::DEFAULT_LOCALE,
        ]);
    }

    public function testGetCatalogueFilters()
    {
        $catalogue = (new FileTranslatedCatalogueProvider(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        ))
            ->getCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomeDomain',
            'ShopSomethingElse',
        ], $domains);

        $provider = new FileTranslatedCatalogueProvider(
            self::$tempDir,
            ['#^ShopSomething([A-Z]|\.|$)#']
        );
        $catalogue = $provider->getCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomethingElse',
        ], $domains);

        $this->assertSame(
            $catalogue->all(),
            $provider->getFileTranslatedCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE)->all()
        );
    }

    public function testGetCatalogueMessages()
    {
        $expectedWordings = [
            'ShopSomeDomain' => [
                'Some wording' => 'Some wording',
                'Some other wording' => 'Some other wording',
            ],
            'ShopSomethingElse' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
            ],
        ];

        $provider = new FileTranslatedCatalogueProvider(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);

        $messages = $catalogue->all();
        sort($messages);

        $this->assertSame(array_values($expectedWordings), $messages);
    }
}
