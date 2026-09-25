<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CarrierByReferenceChoiceProvider;

class CarrierByReferenceChoiceProviderTest extends ChoiceProviderTestCase
{
    private const LANG_ID = 1;

    public function testItLabelsDisabledCarriersWithoutRemovingThem(): void
    {
        $choices = $this->buildProvider([
            ['id_carrier' => 1, 'id_reference' => 1, 'name' => 'Click and collect', 'delay' => 'Pick up in-store', 'active' => '1'],
            ['id_carrier' => 3, 'id_reference' => 3, 'name' => 'My cheap carrier', 'delay' => 'Buy more to pay less!', 'active' => '0'],
        ])->getChoices();

        self::assertSame([
            '1 - Click and collect (Pick up in-store)' => 1,
            '3 - My cheap carrier (Buy more to pay less!) - Disabled' => 3,
        ], $choices);
    }

    /**
     * A disabled carrier must stay selectable: carriers are commonly assigned to products before
     * being turned on, and disabling one temporarily must not detach it from every product.
     */
    public function testDisabledCarriersAreStillOffered(): void
    {
        $choices = $this->buildProvider([
            ['id_carrier' => 3, 'id_reference' => 3, 'name' => 'Disabled one', 'delay' => '', 'active' => '0'],
            ['id_carrier' => 4, 'id_reference' => 4, 'name' => 'Another disabled', 'delay' => '', 'active' => '0'],
        ])->getChoices();

        self::assertCount(2, $choices);
        self::assertSame([3, 4], array_values($choices));
    }

    public function testItLeavesActiveCarrierLabelsUntouched(): void
    {
        $choices = $this->buildProvider([
            ['id_carrier' => 2, 'id_reference' => 2, 'name' => 'My carrier', 'delay' => 'Delivery next day!', 'active' => '1'],
            ['id_carrier' => 5, 'id_reference' => 5, 'name' => 'No delay carrier', 'delay' => '', 'active' => '1'],
        ])->getChoices();

        self::assertSame([
            '2 - My carrier (Delivery next day!)' => 2,
            '5 - No delay carrier' => 5,
        ], $choices);
    }

    /**
     * @param array<int, array<string, mixed>> $carriers
     */
    private function buildProvider(array $carriers): CarrierByReferenceChoiceProvider
    {
        $dataProvider = $this->createMock(CarrierDataProvider::class);
        $dataProvider
            ->method('getCarriers')
            ->willReturn($carriers);

        return new CarrierByReferenceChoiceProvider($dataProvider, self::LANG_ID, $this->mockTranslator());
    }
}
