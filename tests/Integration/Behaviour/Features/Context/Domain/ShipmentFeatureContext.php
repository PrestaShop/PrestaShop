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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetOrderShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ShipmentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then the order :orderReference should have the following shipments:
     *
     * @param string $orderReference
     * @param TableNode $table
     *
     * @throws RuntimeException
     */
    public function verifyOrderShipment(string $orderReference, TableNode $table)
    {
        $data = $table->getColumnsHash();
        $orderId = $this->referenceToId($orderReference);
        $shipments = $this->getQueryBus()->handle(
            new GetOrderShipments($orderId)
        );

        if (count($shipments) === 0) {
            $msg = 'Order [' . $orderId . '] has no shipments';
            throw new RuntimeException($msg);
        }

        for ($i = 0; $i < count($data); ++$i) {
            $shipmentData = $data[$i];
            $shipment = $shipments[$i];
            $carrierId = $this->referenceToId($data[$i]['carrier']);
            $addressId = $this->referenceToId($data[$i]['address']);

            if ($shipment->getOrderId() !== $orderId) {
                throw new RuntimeException('Shipment [' . $shipment->getId() . '] does not belong to order [' . $orderId . ']');
            }

            Assert::assertEquals($shipment->getTrackingNumber(), $shipmentData['tracking_number']);
            Assert::assertEquals($shipment->getCarrierId(), $carrierId);
            Assert::assertEquals($shipment->getAddressId(), $addressId);
            Assert::assertEquals($shipment->getShippingCostTaxExcluded(), $shipmentData['shipping_cost_tax_excl']);
            Assert::assertEquals($shipment->getShippingCostTaxIncluded(), $shipmentData['shipping_cost_tax_incl']);
            SharedStorage::getStorage()->set($shipmentData['shipment'], $shipment->getId());
        }
    }

    /**
     * @Then the shipment :shipmentReference should have the following products:
     *
     * @param string $shipmentReference
     * @param TableNode $table
     */
    public function verifyShipmentProducts(string $shipmentReference, TableNode $table)
    {
        $data = $table->getColumnsHash();
        $shipmentId = SharedStorage::getStorage()->get($shipmentReference);

        $shipmentProducts = $this->getQueryBus()->handle(
            new GetShipmentProducts($shipmentId)
        );

        for ($i = 0; $i < count($shipmentProducts); ++$i) {
            Assert::assertEquals($shipmentProducts[$i]->getQuantity(), (int) $data[$i]['quantity']);
            Assert::assertEquals($shipmentProducts[$i]->getProductName(), $data[$i]['product_name']);
        }
    }
}
