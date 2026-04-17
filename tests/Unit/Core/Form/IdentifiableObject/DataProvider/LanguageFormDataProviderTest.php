<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
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
            'locale' => 'lt-LT',
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
            'locale' => 'lt-LT',
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
                    2,
                    'Lithuanian',
                    'lt',
                    'lt-LT',
                    'lt-LT',
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
