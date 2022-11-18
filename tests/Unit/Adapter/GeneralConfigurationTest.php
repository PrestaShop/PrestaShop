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

namespace Tests\Unit\Adapter\Configure\AdvancedParameters\Administration;

use Cookie;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GeneralConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * @var Cookie|MockObject
     */
    private $mockCookie;

    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

    private const VALID_CONFIGURATION = [
        GeneralType::FIELD_CHECK_MODULES_UPDATE => true,
        GeneralType::FIELD_CHECK_IP_ADDRESS => true,
        GeneralType::FIELD_FRONT_COOKIE_LIFETIME => 480,
        GeneralType::FIELD_BACK_COOKIE_LIFETIME => 480,
        GeneralType::FIELD_COOKIE_SAMESITE => Cookie::SAMESITE_LAX,
    ];

    private const SHOP_ID = 42;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockCookie = $this->createCookieMock();

        $this->generalConfiguration = new GeneralConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockCookie
        );
    }

    /**
     * @return MockObject|Cookie
     */
    protected function createCookieMock()
    {
        return $this->getMockBuilder(Cookie::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $getConfigurationRow = function (string $configurationKey, string $field) use ($shopConstraint): array {
            return [$configurationKey, null, $shopConstraint, self::VALID_CONFIGURATION[$field]];
        };

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    $getConfigurationRow('PRESTASTORE_LIVE', GeneralType::FIELD_CHECK_MODULES_UPDATE),
                    $getConfigurationRow('PS_COOKIE_CHECKIP', GeneralType::FIELD_CHECK_IP_ADDRESS),
                    $getConfigurationRow('PS_COOKIE_LIFETIME_FO', GeneralType::FIELD_FRONT_COOKIE_LIFETIME),
                    $getConfigurationRow('PS_COOKIE_LIFETIME_BO', GeneralType::FIELD_BACK_COOKIE_LIFETIME),
                    $getConfigurationRow('PS_COOKIE_SAMESITE', GeneralType::FIELD_COOKIE_SAMESITE),
                ]
            );

        $result = $this->generalConfiguration->getConfiguration();

        $this->assertSame(
            self::VALID_CONFIGURATION,
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
        $this->expectException($exception);

        $this->generalConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [GeneralType::FIELD_CHECK_MODULES_UPDATE => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [GeneralType::FIELD_CHECK_IP_ADDRESS => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [GeneralType::FIELD_FRONT_COOKIE_LIFETIME => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [GeneralType::FIELD_BACK_COOKIE_LIFETIME => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [GeneralType::FIELD_COOKIE_SAMESITE => 'wrong_value'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $res = $this->generalConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

        $this->assertSame([], $res);
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
