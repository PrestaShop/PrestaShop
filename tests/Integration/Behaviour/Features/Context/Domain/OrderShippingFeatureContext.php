<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCarrierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use RuntimeException;

class OrderShippingFeatureContext extends AbstractDomainFeatureContext
{
    private const CARRIER_MAP = [
        1 => '0',
        2 => 'My carrier',
    ];

    /**
     * @When I update order :orderId Tracking number to :trackingNumber and Carrier to :carrier
     *
     * @param int $orderId
     * @param string $trackingNumber
     * @param string $carrier
     */
    public function updateOrderTrackingNumberToAndCarrierTo(int $orderId, string $trackingNumber, string $carrier)
    {
        $oldOrderCarrierId = $this->getCarrierIdFromMap($carrier);
        $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        $newCarrierId = $this->getCarrierIdFromMap($carrier);

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
     * @Then order :orderId has Carrier :carrier
     *
     * @param string $orderId
     * @param string $carrier
     *
     * @throws RuntimeException
     */
    public function orderHasCarrier(string $orderId, string $carrier)
    {
        $carrierId = $this->getCarrierIdFromMap($carrier);
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
    private function getCarrierIdFromMap(string $carrier)
    {
        $carrierMapFlipped = array_flip(self::CARRIER_MAP);
        if (isset($carrierMapFlipped[$carrier])) {
            /** @var int $carrierId */
            $carrierId = $carrierMapFlipped[$carrier];

            return $carrierId;
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
     * @Then order :orderId has Tracking number :trackingNumber
     *
     * @param int $orderId
     * @param string $trackingNumber
     *
     * @throws RuntimeException
     */
    public function orderHasTrackingNumber(int $orderId, string $trackingNumber)
    {
        $orderCarriersForViewing = $this->getOrderCarriersForViewing($orderId);
        $orderTrackingNumberFromDb = $orderCarriersForViewing[0]->getTrackingNumber();

        if ($trackingNumber !== $orderTrackingNumberFromDb) {
            $msg = 'Order [' . $orderId . '] tracking number is not equal to [' . $trackingNumber . '] ';
            $msg .= 'Received [' . $orderTrackingNumberFromDb . '] ';
            throw new RuntimeException($msg);
        }
    }
}
