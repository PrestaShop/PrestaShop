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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Order;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditOrderAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditOrderAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateOrderAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderDeliveryAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderDeliveryAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderInvoiceAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderAddressType;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShopException;

/**
 * EditOrderAddressCommandHandler manages an address update, it then updates order and cart
 * relation to the newly created address.
 */
class EditOrderAddressHandler implements EditOrderAddressHandlerInterface
{
    /**
     * @var EditCustomerAddressHandlerInterface
     */
    private $addressHandler;

    /**
     * @var ChangeOrderDeliveryAddressHandlerInterface
     */
    private $deliveryAddressHandler;

    /**
     * @var ChangeOrderInvoiceAddressHandlerInterface
     */
    private $invoiceAddressHandler;

    /**
     * @param EditCustomerAddressHandlerInterface $addressHandler
     * @param ChangeOrderDeliveryAddressHandlerInterface $deliveryAddressHandler
     * @param ChangeOrderInvoiceAddressHandlerInterface $invoiceAddressHandler
     */
    public function __construct(
        EditCustomerAddressHandlerInterface $addressHandler,
        ChangeOrderDeliveryAddressHandlerInterface $deliveryAddressHandler,
        ChangeOrderInvoiceAddressHandlerInterface $invoiceAddressHandler
    ) {
        $this->addressHandler = $addressHandler;
        $this->deliveryAddressHandler = $deliveryAddressHandler;
        $this->invoiceAddressHandler = $invoiceAddressHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     * @throws CannotUpdateOrderAddressException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function handle(EditOrderAddressCommand $command): AddressId
    {
        try {
            $addressCommand = $this->createEditAddressCommand($command);
            /** @var AddressId $addressId */
            $addressId = $this->addressHandler->handle($addressCommand);

            switch ($command->getAddressType()) {
                case OrderAddressType::DELIVERY_ADDRESS_TYPE:
                    $this->deliveryAddressHandler->handle(new ChangeOrderDeliveryAddressCommand(
                        $command->getOrderId()->getValue(), $addressId->getValue()
                    ));
                    break;
                case OrderAddressType::INVOICE_ADDRESS_TYPE:
                    $this->invoiceAddressHandler->handle(new ChangeOrderInvoiceAddressCommand(
                        $command->getOrderId()->getValue(), $addressId->getValue()
                    ));
                    break;
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateOrderAddressException(sprintf('An error occurred when updating address for order "%s"', $command->getOrderId()->getValue()));
        }

        return $addressId;
    }

    /**
     * @param EditOrderAddressCommand $orderCommand
     *
     * @return EditCustomerAddressCommand
     *
     * @throws AddressConstraintException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws PrestaShopException
     */
    private function createEditAddressCommand(EditOrderAddressCommand $orderCommand): EditCustomerAddressCommand
    {
        $order = new Order($orderCommand->getOrderId()->getValue());
        $addressId = null;
        switch ($orderCommand->getAddressType()) {
            case OrderAddressType::DELIVERY_ADDRESS_TYPE:
                $addressId = (int) $order->id_address_delivery;
                break;
            case OrderAddressType::INVOICE_ADDRESS_TYPE:
                $addressId = (int) $order->id_address_invoice;
                break;
        }
        $addressCommand = new EditCustomerAddressCommand($addressId);
        if (null !== $orderCommand->getAddressAlias()) {
            $addressCommand->setAddressAlias($orderCommand->getAddressAlias());
        }
        if (null !== $orderCommand->getFirstName()) {
            $addressCommand->setFirstName($orderCommand->getFirstName());
        }
        if (null !== $orderCommand->getLastName()) {
            $addressCommand->setLastName($orderCommand->getLastName());
        }
        if (null !== $orderCommand->getAddress()) {
            $addressCommand->setAddress($orderCommand->getAddress());
        }
        if (null !== $orderCommand->getCity()) {
            $addressCommand->setCity($orderCommand->getCity());
        }
        if (null !== $orderCommand->getPostCode()) {
            $addressCommand->setPostCode($orderCommand->getPostCode());
        }
        if (null !== $orderCommand->getCountryId()) {
            $addressCommand->setCountryId($orderCommand->getCountryId()->getValue());
        }
        if (null !== $orderCommand->getDni()) {
            $addressCommand->setDni($orderCommand->getDni());
        }
        if (null !== $orderCommand->getCompany()) {
            $addressCommand->setCompany($orderCommand->getCompany());
        }
        if (null !== $orderCommand->getVatNumber()) {
            $addressCommand->setVatNumber($orderCommand->getVatNumber());
        }
        if (null !== $orderCommand->getAddress2()) {
            $addressCommand->setAddress2($orderCommand->getAddress2());
        }
        if (null !== $orderCommand->getStateId()) {
            $addressCommand->setStateId($orderCommand->getStateId()->getValue());
        }
        if (null !== $orderCommand->getHomePhone()) {
            $addressCommand->setHomePhone($orderCommand->getHomePhone());
        }
        if (null !== $orderCommand->getMobilePhone()) {
            $addressCommand->setMobilePhone($orderCommand->getMobilePhone());
        }
        if (null !== $orderCommand->getOther()) {
            $addressCommand->setOther($orderCommand->getOther());
        }

        return $addressCommand;
    }
}
