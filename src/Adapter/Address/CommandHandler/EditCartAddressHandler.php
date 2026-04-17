<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Cart;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
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
#[AsCommandHandler]
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
        } catch (PrestaShopException) {
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
