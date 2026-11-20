<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CarrierFormDataHandler;

class CarrierFormDataHandlerTest extends TestCase
{
    /**
     * @dataProvider getIsFree
     */
    public function testCreateSavesTheRangesOnlyWhenShippingIsPaid(bool $isFree): void
    {
        $handledCommands = [];
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(static function (object $command) use (&$handledCommands): CarrierId {
            $handledCommands[] = $command::class;

            return new CarrierId(42);
        });

        (new CarrierFormDataHandler($commandBus))->create($this->getFormData($isFree));

        $this->assertSame(!$isFree, in_array(SetCarrierRangesCommand::class, $handledCommands, true));
    }

    /**
     * @dataProvider getIsFree
     */
    public function testUpdateSavesTheRangesOnlyWhenShippingIsPaid(bool $isFree): void
    {
        $handledCommands = [];
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(static function (object $command) use (&$handledCommands): CarrierId {
            $handledCommands[] = $command::class;

            return new CarrierId(42);
        });

        (new CarrierFormDataHandler($commandBus))->update(42, $this->getFormData($isFree));

        $this->assertSame(!$isFree, in_array(SetCarrierRangesCommand::class, $handledCommands, true));
    }

    /**
     * @return array<string, array<int, bool>>
     */
    public static function getIsFree(): array
    {
        return [
            'free shipping' => [true],
            'paid shipping' => [false],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getFormData(bool $isFree): array
    {
        return [
            'general_settings' => [
                'name' => 'Carrier',
                'localized_delay' => [1 => 'Two days'],
                'grade' => 0,
                'tracking_url' => '',
                'active' => true,
                'group_access' => [1, 2, 3],
                'associated_shops' => [1],
                'logo' => null,
            ],
            'shipping_settings' => [
                'has_additional_handling_fee' => false,
                'is_free' => $isFree,
                'shipping_method' => ShippingMethod::BY_PRICE,
                'range_behavior' => OutOfRangeBehavior::USE_HIGHEST_RANGE,
                'zones' => [1],
                'id_tax_rule_group' => 1,
                'ranges_costs' => [
                    [
                        'zoneId' => 1,
                        'ranges' => [
                            // A negative price can be left in the hidden cost rows of a free carrier
                            ['from' => '0', 'to' => '10', 'price' => $isFree ? '-5' : '5'],
                        ],
                    ],
                ],
            ],
            'size_weight_settings' => [],
        ];
    }
}
