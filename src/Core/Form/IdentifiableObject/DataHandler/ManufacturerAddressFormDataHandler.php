<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Handles submitted manufacturer address form data
 */
final class ManufacturerAddressFormDataHandler implements FormDataHandlerInterface
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
        /** @var AddressId $addressId */
        $addressId = $this->commandBus->handle(new AddManufacturerAddressCommand(
            $data['last_name'],
            $data['first_name'],
            $data['address'],
            $data['id_country'],
            $data['city'],
            $data['id_manufacturer'],
            $data['address2'],
            $data['post_code'],
            $data['id_state'],
            $data['home_phone'],
            $data['mobile_phone'],
            $data['other'],
            $data['dni']
        ));

        return $addressId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($addressId, array $data)
    {
        $command = new EditManufacturerAddressCommand((int) $addressId);
        $this->fillCommandWithData($command, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Fills EditManufacturerAddressCommand with form data
     *
     * @param EditManufacturerAddressCommand $command
     * @param array $data
     *
     * @throws AddressConstraintException
     */
    private function fillCommandWithData(EditManufacturerAddressCommand $command, array $data)
    {
        if (null !== $data['id_manufacturer']) {
            $command->setManufacturerId($data['id_manufacturer']);
        }
        if (null !== $data['last_name']) {
            $command->setLastName($data['last_name']);
        }
        if (null !== $data['first_name']) {
            $command->setFirstName($data['first_name']);
        }
        if (null !== $data['address']) {
            $command->setAddress($data['address']);
        }
        if (null !== $data['id_country']) {
            $command->setCountryId($data['id_country']);
        }
        if (null !== $data['city']) {
            $command->setCity($data['city']);
        }
        if (null !== $data['address2']) {
            $command->setAddress2($data['address2']);
        }
        if (null !== $data['post_code']) {
            $command->setPostCode($data['post_code']);
        }
        if (null !== $data['id_state']) {
            $command->setStateId($data['id_state']);
        }
        if (null !== $data['home_phone']) {
            $command->setHomePhone($data['home_phone']);
        }
        if (null !== $data['mobile_phone']) {
            $command->setMobilePhone($data['mobile_phone']);
        }
        if (null !== $data['other']) {
            $command->setOther($data['other']);
        }
        if (null !== $data['dni']) {
            $command->setDni($data['dni']);
        }
    }
}
