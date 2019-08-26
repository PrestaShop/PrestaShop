<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddModuleCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\Billing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CarrierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int
     */
    private $defaultLangId;

    public function __construct()
    {
        $this->defaultLangId = CommonFeatureContext::getContainer()
            ->get('prestashop.adapter.legacy.configuration')
            ->get('PS_LANG_DEFAULT');
    }

    /**
     * @When I add new carrier :reference with following properties:
     */
    public function addCarrier($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = AddCarrierCommand::withPricedShipping(
            [$this->defaultLangId => $data['carrier_name']],
            [$this->defaultLangId => $data['shipping_delay']],
            (int) $data['speed_grade'],
            $data['tracking_url'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['shipping_cost_included']),
            $this->getBillingValueMap()[$data['billing']],
            (int) $data['tax_rules_group_id'],
            $this->getOutOfRangeBehaviorValueMap()[$data['out_of_range_behavior']],
            $this->formatShippingRanges($data['ranges_from'], $data['ranges_to'], $data['zone_ids'], $data['prices']),
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled'])
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @When I add new carrier :reference with free shipping and following properties:
     */
    public function addFreeShippingCarrier($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = AddCarrierCommand::withFreeShipping(
            [$this->defaultLangId => $data['carrier_name']],
            [$this->defaultLangId => $data['shipping_delay']],
            (int) $data['speed_grade'],
            $data['tracking_url'],
            (int) $data['tax_rules_group_id'],
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled'])
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @When I add new module carrier :reference with free shipping and following properties:
     */
    public function addFreeShippingModuleCarrier($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = AddModuleCarrierCommand::withFreeShipping(
            [$this->defaultLangId => $data['carrier_name']],
            [$this->defaultLangId => $data['shipping_delay']],
            (int) $data['speed_grade'],
            $data['tracking_url'],
            (int) $data['tax_rules_group_id'],
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']),
            $data['module_name']
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @When I add new module carrier :reference with PrestaShop shipping price and following properties:
     */
    public function addModuleCarrierWithCoreShippingPrice($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = AddModuleCarrierCommand::withCoreShippingPrice(
            [$this->defaultLangId => $data['carrier_name']],
            [$this->defaultLangId => $data['shipping_delay']],
            (int) $data['speed_grade'],
            $data['tracking_url'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['shipping_cost_included']),
            $this->getBillingValueMap()[$data['billing']],
            (int) $data['tax_rules_group_id'],
            $this->getOutOfRangeBehaviorValueMap()[$data['out_of_range_behavior']],
            $this->formatShippingRanges($data['ranges_from'], $data['ranges_to'], $data['zone_ids'], $data['prices']),
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']),
            $data['module_name']
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @When I add new module carrier :reference with module shipping price and following properties:
     */
    public function addModuleCarrierWithModuleShippingPrice($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = AddModuleCarrierCommand::withModuleShippingPrice(
            [$this->defaultLangId => $data['carrier_name']],
            [$this->defaultLangId => $data['shipping_delay']],
            (int) $data['speed_grade'],
            $data['tracking_url'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['shipping_cost_included']),
            $this->getBillingValueMap()[$data['billing']],
            (int) $data['tax_rules_group_id'],
            $this->getOutOfRangeBehaviorValueMap()[$data['out_of_range_behavior']],
            $this->formatShippingRanges($data['ranges_from'], $data['ranges_to'], $data['zone_ids'], $data['prices']),
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']),
            $data['module_name'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['module_needs_core_shipping_price'])
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @Then /^carrier "(.+)" should be (enabled|disabled)$/
     */
    public function assertIsEnabled($reference, $status)
    {
        $isEnabled = $status === 'enabled';

        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $actualStatus = (bool) $carrier->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" expected to be %s, but it is %s',
                $reference,
                $status,
                $actualStatus ? 'enabled' : 'disabled'
            ));
        }
    }

    /**
     * @Then /^carrier "(.+)" shipping price should be calculated by (module|PrestaShop)$/
     */
    public function assertShippingPriceIsCalculatedBy($reference, $calculationMethod)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        $isModule = 'module' === $calculationMethod;
        $actualResult = (bool) $carrier->shipping_external;

        if ($isModule !== $actualResult) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" shipping price is calculated by %s, but it was expected it to be calculated by %s',
                $reference,
                $actualResult ? 'module' : 'PrestaShop',
                $calculationMethod
            ));
        }
    }

    /**
     * @Then /^carrier "(.+)" module (should|should not) need the shipping price calculated by PrestaShop$/
     */
    public function assertModuleNeedsCoreShippingPrice($reference, $condition)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $needsPrice = 'should' === $condition;
        $actualResult = (bool) $carrier->need_range;

        if ($needsPrice !== $actualResult) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" module %s the shipping price calculated by PrestaShop, but was expected it to %s',
                $reference,
                $actualResult ? 'needs' : 'does not need',
                $needsPrice ? 'need' : 'not need'
            ));
        }
    }

    /**
     * @Then /^carrier "(.+)" (should|should not) belong to module$/
     */
    public function assertIsModule($reference, $condition)
    {
        $isModule = 'should' === $condition;

        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $actualResult = (bool) $carrier->is_module;

        if ($actualResult !== $isModule) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" %s to module. It was expected that it %s belong to module',
                $reference,
                $actualResult ? 'belongs' : 'does not belong',
                $condition
            ));
        }
    }

    /**
     * @Then carrier :reference :field in default language should be :value
     */
    public function assertLocalizedField($reference, $field, $value)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $field = $this->getObjectPropertyMappedByFieldName($field);

        if (!isset($carrier->{$field}[$this->defaultLangId])) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" "%s" in default language is not set, but "%s" was expected.',
                $reference,
                $field,
                $value
            ));
        }
        if ($value !== $carrier->{$field}[$this->defaultLangId]) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" "%s" in default language is "%s", but "%s" was expected.',
                $reference,
                $field,
                $carrier->{$field}[$this->defaultLangId],
                $value
            ));
        }
    }

    /**
     * @Then carrier :reference :field value should be :value
     */
    public function assertFieldValue($reference, $field, $value)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $propertyName = $this->getObjectPropertyMappedByFieldName($field);

        if ($value !== $carrier->{$propertyName}) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" "%s" is "%s", but "%s" was expected.',
                $reference,
                $field,
                $carrier->{$propertyName},
                $value
            ));
        }
    }

    /**
     * @Then /^carrier "(.+)" billing should be based on package (price|weight)$/
     */
    public function assertBilling($reference, $expectedMethod)
    {
        $billingValueMap = $this->getBillingValueMap();

        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        $actualMethod = (int) $carrier->shipping_method;

        if ($billingValueMap[$expectedMethod] !== $actualMethod) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" billing is based on package %s, but expected to be based on %s',
                $reference,
                $actualMethod === $billingValueMap['price'] ? 'price' : 'weight',
                $expectedMethod
            ));
        }
    }

    /**
     * @Then /^when package is out of carrier "(.+)" range, the (carrier should be disabled|highest range price should be applied)$/
     */
    public function assertOutOfRangeBehavior($reference, $expectedBehavior)
    {
        $behaviorValueMap = $this->getOutOfRangeBehaviorValueMap();

        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);
        $actualBehavior = (int) $carrier->range_behavior;

        if ($behaviorValueMap[$expectedBehavior] !== $actualBehavior) {
            throw new RuntimeException(sprintf(
                'Unexpected out of range behavior. When package is out of carrier "%s" range the %s',
                $reference,
                $actualBehavior === $behaviorValueMap[$expectedBehavior]
            ));
        }
    }

    /**
     * @Then /^the shipping of "(.*)" should be (free of charge|priced)?$/
     */
    public function assertShippingIsFree($reference, $condition)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        $isFree = 'free of charge' === $condition;
        $actualResult = (bool) $carrier->is_free;

        if ($isFree !== $actualResult) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" shipping is "%s", but it expected to be "%s".',
                $reference,
                $actualResult ? 'free of charge' : 'priced',
                $condition
            ));
        }
    }

    /**
     * Maps user friendly field name to a corresponding property of object model
     *
     * @param string $fieldName
     *
     * @return string
     */
    private function getObjectPropertyMappedByFieldName(string $fieldName)
    {
        $objectProperyByFieldNameMap = [
            'speed grade' => 'grade',
            'tracking url' => 'url',
            'max package width' => 'max_width',
            'max package height' => 'max_height',
            'max package depth' => 'max_depth',
            'max package weight' => 'max_weight',
            'module name' => 'external_module_name',
            'shipping delay' => 'delay',
            'localized name' => 'localized_name',
        ];

        if (array_key_exists($fieldName, $objectProperyByFieldNameMap)) {
            return $objectProperyByFieldNameMap[$fieldName];
        }

        return $fieldName;
    }

    /**
     * @param string $rangesFrom
     * @param string $rangesTo
     * @param string $zoneIds
     * @param string $prices
     *
     * @return array
     */
    private function formatShippingRanges(string $rangesFrom, string $rangesTo, string $zoneIds, string $prices)
    {
        $rangesFrom = explode(',', $rangesFrom);
        $rangesTo = explode(',', $rangesTo);
        $zoneIds = explode(',', $zoneIds);
        $prices = explode(',', $prices);

        $pricesByZone = [];
        foreach ($prices as $price) {
            foreach ($zoneIds as $zoneId) {
                $pricesByZone[$zoneId] = $price;
            }
        }

        $ranges = [];
        foreach ($rangesFrom as $key => $from) {
            $ranges[] = [
                'from' => $from,
                'to' => $rangesTo[$key],
                'prices_by_zone_id' => $pricesByZone,
            ];
        }

        return $ranges;
    }

    /**
     * @return array
     */
    private function getBillingValueMap()
    {
        return [
            'price' => Billing::ACCORDING_TO_PRICE,
            'weight' => Billing::ACCORDING_TO_WEIGHT,
        ];
    }

    /**
     * @return array
     */
    private function getOutOfRangeBehaviorValueMap()
    {
        return [
            'carrier should be disabled' => OutOfRangeBehavior::DISABLE_CARRIER,
            'highest range price should be applied' => OutOfRangeBehavior::APPLY_HIGHEST_RANGE,
        ];
    }
}
