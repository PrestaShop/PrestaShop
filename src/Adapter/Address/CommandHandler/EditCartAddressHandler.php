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

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Cart;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCartAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditCartAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateCartAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Cart\CartAddressType;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShopException;

/**
 * EditCartAddressHandler manages an address update, it then updates cart
 * relation to the newly created address.
 */
class EditCartAddressHandler implements EditCartAddressHandlerInterface
{
    /**
     * @var EditCustomerAddressHandlerInterface
     */
    private $addressHandler;

    /**
     * @param EditCustomerAddressHandlerInterface $addressHandler
     */
    public function __construct(EditCustomerAddressHandlerInterface $addressHandler)
    {
        $this->addressHandler = $addressHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     * @throws CannotUpdateCartAddressException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function handle(EditCartAddressCommand $command): AddressId
    {
        try {
            $cart = new Cart($command->getCartId()->getValue());
            if (!Validate::isLoadedObject($cart) || $command->getCartId()->getValue() !== (int) $cart->id) {
                throw new CartNotFoundException(sprintf('Cart with id "%d" was not found', $command->getCartId()->getValue()));
            }

            $addressCommand = $this->createEditAddressCommand($command, $cart);
            /** @var AddressId $addressId */
            $addressId = $this->addressHandler->handle($addressCommand);

            switch ($command->getAddressType()) {
                case CartAddressType::DELIVERY_ADDRESS_TYPE:
                    $cart->id_address_delivery = $addressId->getValue();
                    break;
                case CartAddressType::INVOICE_ADDRESS_TYPE:
                    $cart->id_address_invoice = $addressId->getValue();
                    break;
            }
            if (!$cart->update()) {
                throw new CannotUpdateCartAddressException(sprintf('An error occurred when updating address for cart "%d"', $command->getCartId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateCartAddressException(sprintf('An error occurred when updating address for cart "%d"', $command->getCartId()->getValue()));
        }

        return $addressId;
    }

    /**
     * @param EditCartAddressCommand $cartCommand
     *
     * @return EditCustomerAddressCommand
     *
     * @throws AddressConstraintException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws PrestaShopException
     */
    private function createEditAddressCommand(EditCartAddressCommand $cartCommand, Cart $cart): EditCustomerAddressCommand
    {
        $addressId = null;
        switch ($cartCommand->getAddressType()) {
            case CartAddressType::DELIVERY_ADDRESS_TYPE:
                $addressId = (int) $cart->id_address_delivery;
                break;
            case CartAddressType::INVOICE_ADDRESS_TYPE:
                $addressId = (int) $cart->id_address_invoice;
                break;
        }
        $addressCommand = new EditCustomerAddressCommand($addressId);
        if (null !== $cartCommand->getAddressAlias()) {
            $addressCommand->setAddressAlias($cartCommand->getAddressAlias());
        }
        if (null !== $cartCommand->getFirstName()) {
            $addressCommand->setFirstName($cartCommand->getFirstName());
        }
        if (null !== $cartCommand->getLastName()) {
            $addressCommand->setLastName($cartCommand->getLastName());
        }
        if (null !== $cartCommand->getAddress()) {
            $addressCommand->setAddress($cartCommand->getAddress());
        }
        if (null !== $cartCommand->getCity()) {
            $addressCommand->setCity($cartCommand->getCity());
        }
        if (null !== $cartCommand->getPostCode()) {
            $addressCommand->setPostCode($cartCommand->getPostCode());
        }
        if (null !== $cartCommand->getCountryId()) {
            $addressCommand->setCountryId($cartCommand->getCountryId()->getValue());
        }
        if (null !== $cartCommand->getDni()) {
            $addressCommand->setDni($cartCommand->getDni());
        }
        if (null !== $cartCommand->getCompany()) {
            $addressCommand->setCompany($cartCommand->getCompany());
        }
        if (null !== $cartCommand->getVatNumber()) {
            $addressCommand->setVatNumber($cartCommand->getVatNumber());
        }
        if (null !== $cartCommand->getAddress2()) {
            $addressCommand->setAddress2($cartCommand->getAddress2());
        }
        if (null !== $cartCommand->getStateId()) {
            $addressCommand->setStateId($cartCommand->getStateId()->getValue());
        }
        if (null !== $cartCommand->getHomePhone()) {
            $addressCommand->setHomePhone($cartCommand->getHomePhone());
        }
        if (null !== $cartCommand->getMobilePhone()) {
            $addressCommand->setMobilePhone($cartCommand->getMobilePhone());
        }
        if (null !== $cartCommand->getOther()) {
            $addressCommand->setOther($cartCommand->getOther());
        }

        return $addressCommand;
    }
}
