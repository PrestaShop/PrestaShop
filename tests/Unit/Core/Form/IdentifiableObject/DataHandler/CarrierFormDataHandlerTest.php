<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CarrierFormDataHandler;

/**
 * A carrier priced from its own ranges but saved without any is accepted and then never offered at
 * checkout, with nothing shown to the merchant. These pin the refusal, and - just as important - that
 * the refusal happens before anything is written.
 */
class CarrierFormDataHandlerTest extends TestCase
{
    public function testCreatingACarrierWithNoRangeIsRefusedBeforeAnythingIsSaved(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->never())->method('handle');

        $handler = new CarrierFormDataHandler($commandBus, $this->createMock(CommandBusInterface::class));

        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::MISSING_RANGES);

        $handler->create($this->formData([]));
    }

    public function testAFreeCarrierNeedsNoRange(): void
    {
        $handler = new CarrierFormDataHandler($this->acceptingCommandBus(), $this->createMock(CommandBusInterface::class));

        $handler->create($this->formData([], true));

        $this->addToAssertionCount(1);
    }

    public function testACarrierWithARangeIsAccepted(): void
    {
        $handler = new CarrierFormDataHandler($this->acceptingCommandBus(), $this->createMock(CommandBusInterface::class));

        $handler->create($this->formData($this->oneRange()));

        $this->addToAssertionCount(1);
    }

    /**
     * A range is required exactly when Carrier::getCarriersForOrder() can list the carrier at all, which
     * is what CarrierListedForOrderTest pins against the database.
     *
     * @dataProvider provideCarriersAndWhetherTheyNeedARange
     */
    public function testWhetherAnEditedCarrierNeedsARangeFollowsHowItIsPriced(
        bool $isModule,
        bool $needRange,
        bool $expectsRefusal
    ): void {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturn($this->carrier($isModule, $needRange));

        $handler = new CarrierFormDataHandler($this->acceptingCommandBus(), $queryBus);

        if ($expectsRefusal) {
            $this->expectException(CarrierConstraintException::class);
            $this->expectExceptionCode(CarrierConstraintException::MISSING_RANGES);
        }

        $handler->update(1, $this->formData([]));

        $this->addToAssertionCount(1);
    }

    public function provideCarriersAndWhetherTheyNeedARange(): iterable
    {
        // listed by the checkout once they have a range
        yield 'shop carrier' => [false, false, true];
        yield 'shop carrier that asks for ranges' => [false, true, true];
        yield 'module carrier that keeps its ranges' => [true, true, true];
        // never listed by the native checkout, with or without a range
        yield 'module carrier with need_range off' => [true, false, false];
    }

    private function acceptingCommandBus(): CommandBusInterface
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->method('handle')->willReturn(new CarrierId(1));

        return $bus;
    }

    private function carrier(bool $isModule, bool $needRange): EditableCarrier
    {
        return new EditableCarrier(
            carrierId: 1,
            name: 'carrier',
            grade: 0,
            trackingUrl: '',
            position: 0,
            active: true,
            delay: [1 => 'delay'],
            max_width: 0,
            max_height: 0,
            max_depth: 0,
            max_weight: 0.0,
            associatedGroupIds: [],
            hasAdditionalHandlingFee: false,
            isFree: false,
            shippingMethod: 1,
            idTaxRuleGroup: 0,
            rangeBehavior: 0,
            associatedShopIds: [1],
            zones: [1],
            module: $isModule,
            needRange: $needRange,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $zones
     */
    private function formData(array $zones, bool $isFree = false): array
    {
        return [
            'general_settings' => [
                'name' => 'carrier',
                'localized_delay' => [1 => 'delay'],
                'grade' => 0,
                'tracking_url' => '',
                'active' => true,
                'group_access' => [],
                'associated_shops' => [1],
                'logo' => null,
            ],
            'shipping_settings' => [
                'has_additional_handling_fee' => false,
                'is_free' => $isFree,
                'shipping_method' => 1,
                'range_behavior' => 0,
                'zones' => [1],
                'id_tax_rule_group' => 0,
                'ranges_costs' => $zones,
            ],
            'size_weight_settings' => [
                'max_width' => 0, 'max_height' => 0, 'max_depth' => 0, 'max_weight' => 0,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function oneRange(): array
    {
        return [
            ['zoneId' => 1, 'ranges' => [['from' => 0, 'to' => 1000, 'price' => '5']]],
        ];
    }
}
