<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Address;
use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\AddSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShopDatabaseException;
use PrestaShopException;
use Supplier;

/**
 * Handles command which adds new supplier using legacy object model
 */
#[AsCommandHandler]
final class AddSupplierHandler extends AbstractSupplierHandler implements AddSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(AddSupplierCommand $command)
    {
        $supplier = new Supplier();
        $this->fillSupplierWithData($supplier, $command);
        $address = $this->fetchSupplierAddressFromCommand($command);

        try {
            $this->validateFields($supplier, $address);

            if (!$address->add()) {
                throw new SupplierException(sprintf('Failed to add new supplier address "%s"', $address->address1));
            }

            if (!$supplier->add()) {
                throw new SupplierException(sprintf('Failed to add new supplier "%s"', $command->getName()));
            }

            $this->addShopAssociation($supplier, $command);
            $address->id_supplier = $supplier->id;
            $address->update();
        } catch (PrestaShopException) {
            throw new SupplierException(sprintf('Failed to add new supplier "%s"', $command->getName()));
        }

        return new SupplierId((int) $supplier->id);
    }

    /**
     * Add supplier and shop association
     *
     * @param Supplier $supplier
     * @param AddSupplierCommand $command
     *
     * @throws PrestaShopDatabaseException
     */
    private function addShopAssociation(Supplier $supplier, AddSupplierCommand $command)
    {
        $this->associateWithShops(
            $supplier,
            $command->getShopAssociation()
        );
    }

    /**
     * @param Supplier $supplier
     * @param AddSupplierCommand $command
     */
    private function fillSupplierWithData(Supplier $supplier, AddSupplierCommand $command)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $supplier->name = $command->getName();
        $supplier->description = $command->getLocalizedDescriptions();
        $supplier->meta_description = $command->getLocalizedMetaDescriptions();
        $supplier->meta_title = $command->getLocalizedMetaTitles();
        $supplier->date_add = $currentDateTime;
        $supplier->date_upd = $currentDateTime;
        $supplier->active = $command->isEnabled();
    }

    /**
     * Creates legacy address from given command data
     *
     * @param AddSupplierCommand $command
     *
     * @return Address
     */
    private function fetchSupplierAddressFromCommand(AddSupplierCommand $command)
    {
        $address = new Address();
        $address->alias = 'supplier';
        $address->firstname = 'supplier';
        $address->lastname = 'supplier';
        $address->address1 = $command->getAddress();
        $address->address2 = $command->getAddress2();
        $address->id_country = $command->getCountryId();
        $address->city = $command->getCity();
        $address->id_state = $command->getStateId();
        $address->phone = $command->getPhone();
        $address->phone_mobile = $command->getMobilePhone();
        $address->postcode = $command->getPostCode();
        $address->dni = $command->getDni();

        return $address;
    }
}
