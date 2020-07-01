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
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\DefaultCatalogueProvider;
use PrestaShopBundle\Translation\Provider\UserTranslatedCatalogueProvider;
use Symfony\Component\Translation\MessageCatalogue;

class UserTranslatedCatalogueProviderTest extends TestCase
{
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

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DatabaseTranslationLoader
     */
    private $databaseLoader;

    public function setUp()
    {
        $catalogue = new MessageCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }

        $this->databaseLoader = $this->createMock(DatabaseTranslationLoader::class);
        $this->databaseLoader
            ->method('load')
            ->willReturn($catalogue);
    }

    public function testGetCatalogue()
    {
        $provider = new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            ['^ShopSomeDomain([A-Za-z]|$)']
        );

        $catalogue = $provider->getCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomeDomain',
            'ShopSomethingElse',
        ], $domains);

        $this->assertSame(
            $catalogue->all(),
            $provider->getUserTranslatedCatalogue(DefaultCatalogueProvider::DEFAULT_LOCALE)->all()
        );
    }
}
