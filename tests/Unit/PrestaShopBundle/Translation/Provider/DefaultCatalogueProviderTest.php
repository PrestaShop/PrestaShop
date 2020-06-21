<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\DefaultCatalogueProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class DefaultCatalogueProviderTest extends TestCase
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

    public static function setUpBeforeClass()
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'DefaultCatalogueProviderTest']);

        $catalogue = new MessageCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir,
        ]);
    }

    public function testGetCatalogueFilters()
    {
        $catalogue = (new DefaultCatalogueProvider(
            DefaultCatalogueProvider::DEFAULT_LOCALE,
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        ))
            ->getCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomeDomain',
            'ShopSomethingElse',
        ], $domains);
        $provider = new DefaultCatalogueProvider(
            DefaultCatalogueProvider::DEFAULT_LOCALE,
            self::$tempDir,
            ['#^ShopSomething([A-Z]|\.|$)#']
        );
        $catalogue = $provider->getCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomethingElse',
        ], $domains);

        $this->assertSame($catalogue->all(), $provider->getDefaultCatalogue()->all());
    }

    public function testGetCatalogueMessages()
    {
        $provider = new DefaultCatalogueProvider(
            DefaultCatalogueProvider::DEFAULT_LOCALE,
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue();

        $messages = $catalogue->all();
        sort($messages);

        $this->assertSame(array_values(self::$wordings), $messages);
    }

    public function testGetCatalogueEmpty()
    {
        $provider = new DefaultCatalogueProvider(
            DefaultCatalogueProvider::DEFAULT_LOCALE,
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(true);

        $messages = $catalogue->all();
        sort($messages);

        $this->assertSame(array_values(self::$wordings), $messages);

        $provider = new DefaultCatalogueProvider(
            'ab-AB',
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(true);

        $messages = $catalogue->all();
        sort($messages);

        $this->assertSame(array_values(self::$emptyWordings), $messages);
    }
}
