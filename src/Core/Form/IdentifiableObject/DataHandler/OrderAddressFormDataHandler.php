<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditOrderAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAddressTypeException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;

class OrderAddressFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        // Not used for creation, only edition
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws InvalidAddressTypeException
     * @throws OrderException
     */
    public function update($orderId, array $data)
    {
        $editAddressCommand = new EditOrderAddressCommand($orderId, $data['address_type']);

        if (isset($data['alias'])) {
            $editAddressCommand->setAddressAlias($data['alias']);
        }

        if (isset($data['first_name'])) {
            $editAddressCommand->setFirstName($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $editAddressCommand->setLastName($data['last_name']);
        }

        if (isset($data['address1'])) {
            $editAddressCommand->setAddress($data['address1']);
        }

        if (isset($data['city'])) {
            $editAddressCommand->setCity($data['city']);
        }

        if (isset($data['id_country'])) {
            $editAddressCommand->setCountryId((int) $data['id_country']);
        }

        if (isset($data['postcode'])) {
            $editAddressCommand->setPostCode($data['postcode']);
        }

        if (isset($data['dni'])) {
            $editAddressCommand->setDni($data['dni']);
        }

        if (isset($data['company'])) {
            $editAddressCommand->setCompany($data['company']);
        }

        if (isset($data['vat_number'])) {
            $editAddressCommand->setVatNumber($data['vat_number']);
        }

        if (isset($data['address2'])) {
            $editAddressCommand->setAddress2($data['address2']);
        }

        if (isset($data['id_state'])) {
            $editAddressCommand->setStateId((int) $data['id_state']);
        }

        if (isset($data['phone'])) {
            $editAddressCommand->setHomePhone($data['phone']);
        }

        if (isset($data['phone_mobile'])) {
            $editAddressCommand->setMobilePhone($data['phone_mobile']);
        }

        if (isset($data['other'])) {
            $editAddressCommand->setOther($data['other']);
        }

        $this->commandBus->handle($editAddressCommand);
    }
}
