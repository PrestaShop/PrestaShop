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

namespace Tests\Unit\Adapter\Upload;

use PrestaShop\PrestaShop\Adapter\Upload\UploadQuotaConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\UploadQuotaType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class UploadQuotaConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * @var UploadQuotaConfiguration
     */
    private $uploadQuotaConfiguration;

    private const VALID_CONFIGURATION = [
        UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES => 1,
        UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE => 1,
        UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE => 1
    ];

    private const SHOP_ID = 42;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uploadQuotaConfiguration = new UploadQuotaConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );
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

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_ATTACHMENT_MAXIMUM_SIZE', null, $shopConstraint, self::VALID_CONFIGURATION[UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES]],
                    ['PS_LIMIT_UPLOAD_FILE_VALUE', null, $shopConstraint, self::VALID_CONFIGURATION[UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE]],
                    ['PS_LIMIT_UPLOAD_IMAGE_VALUE', null, $shopConstraint, self::VALID_CONFIGURATION[UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE]],
                ]
            );

        $result = $this->uploadQuotaConfiguration->getConfiguration();

        $this->assertSame(self::VALID_CONFIGURATION, $result);
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

        $this->uploadQuotaConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $res = $this->uploadQuotaConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
