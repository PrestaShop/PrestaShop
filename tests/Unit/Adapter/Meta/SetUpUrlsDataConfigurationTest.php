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

namespace Tests\Unit\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
use PrestaShop\PrestaShop\Adapter\Meta\SetUpUrlsDataConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\TestCase\AbstractConfigurationTestCase;

class SetUpUrlsDataConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'accented_url' => true,
        'canonical_url_redirection' => 2,
        'disable_apache_multiview' => true,
        'disable_apache_mod_security' => true,
    ];

    /**
     * @var HtaccessFileGenerator
     */
    private $mockHtaccessFileGenerator;

    /**
     * @var TranslatorInterface
     */
    private $mockTranslator;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();
        $this->mockHtaccessFileGenerator = $this->createHtaccessFileGeneratorMock();
        $this->mockTranslator = $this->createTranslatorMock();
    }

    /**
     * @return HtaccessFileGenerator
     */
    protected function createHtaccessFileGeneratorMock(): HtaccessFileGenerator
    {
        $stub = $this->getMockBuilder(HtaccessFileGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('generateFile')
            ->willReturn(true);

        return $stub;
    }

    /**
     * @return TranslatorInterface
     */
    protected function createTranslatorMock(): TranslatorInterface
    {
        return $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $setUpUrlsDataConfiguration = new SetUpUrlsDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockHtaccessFileGenerator,
            $this->mockTranslator
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_ALLOW_ACCENTED_CHARS_URL', false, $shopConstraint, true],
                    ['PS_CANONICAL_REDIRECT', 0, $shopConstraint, 2],
                    ['PS_HTACCESS_DISABLE_MULTIVIEWS', false, $shopConstraint, true],
                    ['PS_HTACCESS_DISABLE_MODSEC', false, $shopConstraint, true],
                ]
            );

        $result = $setUpUrlsDataConfiguration->getConfiguration();
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
        $setUpUrlsDataConfiguration = new SetUpUrlsDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockHtaccessFileGenerator,
            $this->mockTranslator
        );

        $this->expectException($exception);
        $setUpUrlsDataConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['accented_url' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['canonical_url_redirection' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['disable_apache_multiview' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['disable_apache_mod_security' => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $setUpUrlsDataConfiguration = new SetUpUrlsDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockHtaccessFileGenerator,
            $this->mockTranslator
        );

        $res = $setUpUrlsDataConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
