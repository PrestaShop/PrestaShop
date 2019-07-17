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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CarrierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int
     */
    private $defaultLangId;

    public function __construct()
    {
        $this->defaultLangId = CommonFeatureContext::getContainer()
            ->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT');
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
            (bool) $data['shipping_cost_included'],
            (int) $data['shipping_method'],
            (int) $data['tax_rules_group_id'],
            (int) $data['out_of_range_behavior'],
            $this->formatShippingRanges($data['ranges_from'], $data['ranges_to'], $data['zone_ids'], $data['prices']),
            (int) $data['max_width'],
            (int) $data['max_height'],
            (int) $data['max_depth'],
            (float) $data['max_weight'],
            explode(',', $data['group_ids']),
            explode(',', $data['shop_ids'])
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
            explode(',', $data['shop_ids'])
        );

        /** @var CarrierId $carrierId */
        $carrierId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Carrier($carrierId->getValue()));
    }

    /**
     * @Then Carrier :reference name in default language should be :value
     */
    public function assertLocalizedName($reference, $value)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        if (!isset($carrier->localized_name[$this->defaultLangId])) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" name in default language is not set, "%s" was expected.',
                $reference,
                $value
            ));
        }
        if ($carrier->localized_name[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" name in default language is "%s", but "%s" was expected.',
                $reference,
                $carrier->localized_name[$this->defaultLangId],
                $value
            ));
        }
    }

    /**
     * @Then Carrier :reference shipping delay in default language should be :value
     */
    public function assertShippingDelay($reference, $value)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        if (!isset($carrier->delay[$this->defaultLangId])) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" shipping delay in default language is not set, but "%s" was expected.',
                $reference,
                $value
            ));
        }
        if ($value !== $carrier->delay[$this->defaultLangId]) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" shipping delay in default language is "%s", but "%s" was expected.',
                $reference,
                $carrier->localized_name[$this->defaultLangId],
                $value
            ));
        }
    }

    /**
     * @Then Carrier :reference :field should be :value
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
     * @Then /^the shipping of "(.*)" should be (free of charge|priced)?$/
     */
    public function assertShippingIsFree($reference, $condition)
    {
        /** @var Carrier $carrier */
        $carrier = SharedStorage::getStorage()->get($reference);

        $isFree = true;
        if ('priced' === $condition) {
            $isFree = false;
        }
        if ($isFree !== (bool) $carrier->is_free) {
            throw new RuntimeException(sprintf(
                'Carrier "%s" shipping is "%s", but it expected to be "%s".',
                $reference,
                $carrier->is_free ? 'free of charge' : 'priced',
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
            'shipping method' => 'shipping_method',
            'out of range behavior' => 'range_behavior',
            'max package width' => 'max_width',
            'max package height' => 'max_height',
            'max package depth' => 'max_depth',
            'max package weight' => 'max_weight',
        ];

        if (array_key_exists($fieldName, $objectProperyByFieldNameMap)) {
            return $objectProperyByFieldNameMap[$fieldName];
        }

        return $fieldName;
    }

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
}
