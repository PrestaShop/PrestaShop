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

namespace Tests\Unit\Adapter\Media;

use PrestaShop\PrestaShop\Adapter\Media\MediaServerConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class MediaServerConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $exampleUrl1 = 'example1.com';
        $exampleUrl2 = 'example2.com';
        $exampleUrl3 = 'example3.com';

        $mediaServerConfiguration = new MediaServerConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_MEDIA_SERVER_1', null, $shopConstraint, $exampleUrl1],
                    ['PS_MEDIA_SERVER_2', null, $shopConstraint, $exampleUrl2],
                    ['PS_MEDIA_SERVER_3', null, $shopConstraint, $exampleUrl3],
                ]
            );

        $result = $mediaServerConfiguration->getConfiguration();
        $this->assertSame(
            [
                'media_server_one' => $exampleUrl1,
                'media_server_two' => $exampleUrl2,
                'media_server_three' => $exampleUrl3,
            ],
            $result
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $HandlingConfiguration = new MediaServerConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $HandlingConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        $invalidUrl = '.';
        $validUrl = 'https://example.com/';

        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['media_server_one' => 'wrong_value', 'media_server_two' => $validUrl, 'media_server_three' => $validUrl]],
            [InvalidOptionsException::class, ['media_server_one' => $validUrl, 'media_server_two' => $invalidUrl, 'media_server_three' => $validUrl]],
            [InvalidOptionsException::class, ['media_server_one' => $validUrl, 'media_server_two' => $validUrl, 'media_server_three' => $invalidUrl]],
        ];
    }

    /**
     * @dataProvider provideSuccessfulUpdate
     *
     * @param string $serverOne
     * @param string $serverTwo
     * @param string $serverThree
     * @param int $hasMediaServer
     */
    public function testSuccessfulUpdate(string $serverOne, string $serverTwo, string $serverThree, int $hasMediaServer): void
    {
        $HandlingConfiguration = new MediaServerConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);
        $this->mockConfiguration
            ->expects($this->exactly(4))
            ->method('set')
            ->withConsecutive(
                ['PS_MEDIA_SERVER_1', $serverOne],
                ['PS_MEDIA_SERVER_2', $serverTwo],
                ['PS_MEDIA_SERVER_3', $serverThree],
                ['PS_MEDIA_SERVERS', $hasMediaServer]
            );

        $res = $HandlingConfiguration->updateConfiguration([
            'media_server_one' => $serverOne,
            'media_server_two' => $serverTwo,
            'media_server_three' => $serverThree,
        ]);

        $this->assertSame([], $res);
    }

    /**
     * @return array[]
     */
    public function provideSuccessfulUpdate(): iterable
    {
        yield ['example.com', '', 'www.example.com', 1];
        yield ['', '', '', 0];
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
