<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCarrierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CarrierByReferenceChoiceProvider;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderShippingFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I update order :orderReference Tracking number to :trackingNumber and Carrier to :carrier
     *
     * @param string $orderReference
     * @param string $trackingNumber
     * @param string $carrier
     */
    public function updateOrderTrackingNumberToAndCarrierTo(
        string $orderReference, string $trackingNumber, string $carrier
    ) {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $oldOrderCarrierId = $this->getCarrierId($carrier);
        $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        $newCarrierId = $this->getCarrierId($carrier);

        $this->getCommandBus()->handle(
            new UpdateOrderShippingDetailsCommand(
                $orderId,
                $oldOrderCarrierId,
                $newCarrierId,
                $trackingNumber
            )
        );
    }

    /**
     * @Then order :orderReference has Carrier :carrier
     *
     * @param string $orderReference
     * @param string $carrier
     *
     * @throws RuntimeException
     */
    public function orderHasCarrier(string $orderReference, string $carrier)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $carrierId = $this->getCarrierId($carrier);

        /** @var OrderCarrierForViewing[] $orderCarriersForViewing */
        $orderCarriersForViewing = $this->getOrderCarriersForViewing($orderId);
        $carrierIdFromDb = $orderCarriersForViewing[0]->getCarrierId();

        if ($carrierId !== $carrierIdFromDb) {
            $msg = 'Order [' . $orderId . '] carrier id is not equal to [' . $carrierId . '] ';
            $msg .= 'Received [' . $carrierIdFromDb . '] ';
            throw new RuntimeException($msg);
        }
    }

    /**
     * @param string $carrier
     *
     * @return int
     *
     * @throws RuntimeException
     */
    private function getCarrierId(string $carrier)
    {
        /** @var CarrierByReferenceChoiceProvider $carrierChoiceProvider */
        $carrierChoiceProvider = $this->getContainer()
                                      ->get('prestashop.core.form.choice_provider.carrier_by_reference_id');
        $availableCarriers = $carrierChoiceProvider->getChoices();

        if (isset($availableCarriers[$carrier])) {
            return (int) $availableCarriers[$carrier];
        }
        throw new RuntimeException('Invalid carrier [' . $carrier . ']');
    }

    /**
     * @param int $orderId
     *
     * @return array|OrderCarrierForViewing[]
     *
     * @throws RuntimeException
     */
    private function getOrderCarriersForViewing(int $orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderCarrierForViewing[] $orderCarriers */
        $orderCarriersForViewing = $orderForViewing->getShipping()->getCarriers();

        if (count($orderCarriersForViewing) == 0) {
            $msg = 'Order [' . $orderId . '] has no carriers';
            throw new RuntimeException($msg);
        }

        return $orderCarriersForViewing;
    }

    /**
     * @Then order :orderReference has Tracking number :trackingNumber
     *
     * @param string $orderReference
     * @param string $trackingNumber
     *
     * @throws RuntimeException
     */
    public function orderHasTrackingNumber(string $orderReference, string $trackingNumber)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $orderCarriersForViewing = $this->getOrderCarriersForViewing($orderId);
        $orderTrackingNumberFromDb = $orderCarriersForViewing[0]->getTrackingNumber();

        if ($trackingNumber !== $orderTrackingNumberFromDb) {
            $msg = 'Order [' . $orderId . '] tracking number is not equal to [' . $trackingNumber . '] ';
            $msg .= 'Received [' . $orderTrackingNumberFromDb . '] ';
            throw new RuntimeException($msg);
        }
    }
}
