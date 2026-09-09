<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierTaxRuleGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CarrierFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    public function create(array $data)
    {
        // A carrier created here is never a module carrier, so it is always priced from its own ranges.
        $this->assertRangesAreDefined($data, true);

        /** @var UploadedFile|null $logo */
        $logo = $data['general_settings']['logo'];
        if ($logo instanceof UploadedFile) {
            $logoPath = $logo->getPathname();
        } else {
            $logoPath = null;
        }

        /** @var CarrierId $carrierId */
        $carrierId = $this->commandBus->handle(new AddCarrierCommand(
            $data['general_settings']['name'],
            $data['general_settings']['localized_delay'],
            $data['general_settings']['grade'],
            $data['general_settings']['tracking_url'] ?? '',
            (bool) $data['general_settings']['active'],
            $data['general_settings']['group_access'],
            (bool) $data['shipping_settings']['has_additional_handling_fee'],
            (bool) $data['shipping_settings']['is_free'],
            $data['shipping_settings']['shipping_method'],
            $data['shipping_settings']['range_behavior'],
            $data['shipping_settings']['zones'],
            $data['general_settings']['associated_shops'],
            $data['size_weight_settings']['max_width'] ?? 0,
            $data['size_weight_settings']['max_height'] ?? 0,
            $data['size_weight_settings']['max_depth'] ?? 0,
            $data['size_weight_settings']['max_weight'] ?? 0,
            $logoPath,
        ));

        // Then, we need to add ranges for this carrier
        $carrierId = $this->setCarrierRange($carrierId, $data);

        // Finally we save the tax rules group
        $carrierId = $this->setCarrierTaxRuleGroup($carrierId, $data);

        return $carrierId->getValue();
    }

    public function update($id, array $data)
    {
        /** @var EditableCarrier $carrier */
        $carrier = $this->queryBus->handle(new GetCarrierForEditing((int) $id, ShopConstraint::allShops()));
        $this->assertRangesAreDefined($data, $this->isPricedFromItsOwnRanges($carrier));

        // If the courier has no costs, 'has_additional_handling_fee' must be false.
        if ((bool) $data['shipping_settings']['is_free'] === true) {
            $data['shipping_settings']['has_additional_handling_fee'] = false;
        }

        // First, we need to update the general settings of the carrier
        $command = new EditCarrierCommand($id);
        $command
            ->setName($data['general_settings']['name'])
            ->setLocalizedDelay($data['general_settings']['localized_delay'])
            ->setGrade($data['general_settings']['grade'])
            ->setActive((bool) $data['general_settings']['active'])
            ->setTrackingUrl($data['general_settings']['tracking_url'] ?? '')
            ->setAdditionalHandlingFee((bool) $data['shipping_settings']['has_additional_handling_fee'])
            ->setIsFree((bool) $data['shipping_settings']['is_free'])
            ->setShippingMethod($data['shipping_settings']['shipping_method'])
            ->setRangeBehavior($data['shipping_settings']['range_behavior'])
            ->setMaxWidth($data['size_weight_settings']['max_width'] ?? null)
            ->setMaxHeight($data['size_weight_settings']['max_height'] ?? null)
            ->setMaxDepth($data['size_weight_settings']['max_depth'] ?? null)
            ->setMaxWeight($data['size_weight_settings']['max_weight'] ?? null)
            ->setAssociatedGroupIds($data['general_settings']['group_access'] ?? null)
            ->setZones($data['shipping_settings']['zones'])
        ;
        /** @var UploadedFile|null $logo */
        $logo = $data['general_settings']['logo'];
        if ($logo instanceof UploadedFile) {
            $command->setLogoPathName($logo->getPathname());
        }

        /** @var CarrierId $carrierId */
        $carrierId = $this->commandBus->handle($command);

        // Then, we need to update the shipping ranges of the carrier only if shipping is paid
        if (!$data['shipping_settings']['is_free']) {
            $carrierId = $this->setCarrierRange($carrierId, $data);
        }

        // Finally we save the tax rules group
        $carrierId = $this->setCarrierTaxRuleGroup($carrierId, $data);

        return $carrierId->getValue();
    }

    /**
     * A carrier that is priced from its ranges but has none is accepted by the back office and then never
     * offered at checkout, with nothing to tell the merchant why - the reported defect. Refuse the save
     * instead, so the message arrives where the mistake was made.
     *
     * A free carrier is exempt because it has no cost to look up: update() already skips saving ranges for
     * one, and requiring them would block a shop that offers free delivery.
     */
    private function assertRangesAreDefined(array $data, bool $pricedFromItsOwnRanges): void
    {
        if (!$pricedFromItsOwnRanges || (bool) $data['shipping_settings']['is_free']) {
            return;
        }

        if ([] !== $this->formatFormRangesData($data)) {
            return;
        }

        throw new CarrierConstraintException(
            'A carrier priced from its own ranges must have at least one range.',
            CarrierConstraintException::MISSING_RANGES
        );
    }

    /**
     * WHY these two flags and not is_module: Cart::getPackageShippingCostFromModule() is what decides.
     * It returns the range price untouched unless shipping_external is set, and when a module carrier also
     * turns need_range off the module replaces the cost entirely - that is the only case where a carrier
     * legitimately has no ranges. A module carrier that keeps need_range on is still priced from them.
     */
    private function isPricedFromItsOwnRanges(EditableCarrier $carrier): bool
    {
        return !$carrier->isShippingExternal() || $carrier->needsRange();
    }

    /**
     * Function aim to format ranges data from the form, to be used in the command of Setting carrier ranges.
     */
    private function formatFormRangesData(array $data): array
    {
        $ranges = [];
        $data = $data['shipping_settings']['ranges_costs'] ?? [];

        foreach ($data as $zone) {
            foreach ($zone['ranges'] as $range) {
                $ranges[] = [
                    'id_zone' => $zone['zoneId'],
                    'range_from' => $range['from'],
                    'range_to' => $range['to'],
                    'range_price' => $range['price'],
                ];
            }
        }

        return $ranges;
    }

    /**
     * Save the carrier ranges.
     */
    private function setCarrierRange(CarrierId $carrierId, array $data): CarrierId
    {
        // We format the ranges data from the form, and create the command object.
        $rangesData = $this->formatFormRangesData($data);
        $rangesCommand = new SetCarrierRangesCommand(
            $carrierId->getValue(),
            $rangesData,
            ShopConstraint::allShops()
        );
        // Then, we handle the command to save the ranges.
        /** @var CarrierId $newCarrierId */
        $newCarrierId = $this->commandBus->handle($rangesCommand);

        return $newCarrierId;
    }

    private function setCarrierTaxRuleGroup(CarrierId $carrierId, array $data): CarrierId
    {
        $taxRuleCommand = new SetCarrierTaxRuleGroupCommand(
            $carrierId->getValue(),
            $data['shipping_settings']['id_tax_rule_group'],
            ShopConstraint::allShops()
        );

        /** @var CarrierId $newCarrierId */
        $newCarrierId = $this->commandBus->handle($taxRuleCommand);

        return $newCarrierId;
    }
}
