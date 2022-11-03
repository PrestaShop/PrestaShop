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

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Address;
use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
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
        } catch (PrestaShopException $e) {
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
        $supplier->meta_keywords = $command->getLocalizedMetaKeywords();
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
