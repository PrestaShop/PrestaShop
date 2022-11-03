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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\LanguageFormDataProvider;

class LanguageFormDataProviderTest extends TestCase
{
    public function testItProvideFormDataForLanguageEditingWhenMultistoreFeatureIsOff()
    {
        $formDataProvider = new LanguageFormDataProvider(
            $this->createQueryBusMock(),
            false,
            [1]
        );

        $this->assertEquals([
            'name' => 'Lithuanian',
            'iso_code' => 'lt',
            'tag_ietf' => 'lt-LT',
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
        ], $formDataProvider->getData(2));
    }

    public function testItProvideFormDataForLanguageEditingWhenMultistoreFeatureIsUsed()
    {
        $formDataProvider = new LanguageFormDataProvider(
            $this->createQueryBusMock(),
            true,
            [1]
        );

        $this->assertEquals([
            'name' => 'Lithuanian',
            'iso_code' => 'lt',
            'tag_ietf' => 'lt-LT',
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
            'shop_association' => [1, 2],
        ], $formDataProvider->getData(2));
    }

    public function tetItProvidesDefaultFormDataForLanguageCreatingWhenMultistoreFeatureIsOff()
    {
        $formDataProvider = new LanguageFormDataProvider(
            $this->createQueryBusMock(),
            false,
            [1]
        );

        $this->assertEquals([
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
        ], $formDataProvider->getDefaultData());
    }

    public function tetItProvidesDefaultFormDataForLanguageCreatingWhenMultistoreFeatureIsUsed()
    {
        $defaultShopAssociation = [1, 2, 3];

        $formDataProvider = new LanguageFormDataProvider(
            $this->createQueryBusMock(),
            true,
            $defaultShopAssociation
        );

        $this->assertEquals([
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
            'shop_association' => $defaultShopAssociation,
        ], $formDataProvider->getDefaultData());
    }

    private function createQueryBusMock()
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetLanguageForEditing::class))
            ->willReturn(
                new EditableLanguage(
                    new LanguageId(2),
                    'Lithuanian',
                    new IsoCode('lt'),
                    new TagIETF('lt-LT'),
                    'Y-m-d',
                    'Y-m-d H:i:s',
                    false,
                    true,
                    [1, 2]
                )
            )
        ;

        return $queryBus;
    }
}
