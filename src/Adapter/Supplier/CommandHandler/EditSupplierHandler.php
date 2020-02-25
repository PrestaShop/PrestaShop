<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Address;
use Supplier;
use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\EditSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\EditSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShopException;

/**
 * Handles command which edits supplier using legacy object model
 */
final class EditSupplierHandler extends AbstractSupplierHandler implements EditSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(EditSupplierCommand $command)
    {
        $supplierId = $command->getSupplierId();
        $supplier = $this->getSupplier($supplierId);
        $address = $this->getSupplierAddress($supplierId);

        $this->populateSupplierWithData($supplier, $command);
        $this->populateAddressWithData($address, $command);

        try {
            $this->validateFields($supplier, $address);

            if (false === $supplier->update()) {
                throw new SupplierException(
                    sprintf('Cannot update supplier with id "%s"', $supplier->id)
                );
            }
            if (false === $address->update()) {
                throw new SupplierException(
                    sprintf('Cannot update supplier address with id "%s"', $address->id)
                );
            }
            if (null !== $command->getAssociatedShops()) {
                $this->associateWithShops($supplier, $command->getAssociatedShops());
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException(
                sprintf('Cannot update supplier with id "%s"', $supplier->id)
            );
        }
    }

    /**
     * Populates Supplier object with given data
     *
     * @param Supplier $supplier
     * @param EditSupplierCommand $command
     */
    private function populateSupplierWithData(Supplier $supplier, EditSupplierCommand $command)
    {
        if (null !== $command->getName()) {
            $supplier->name = $command->getName();
        }
        if (null !== $command->getLocalizedDescriptions()) {
            $supplier->description = $command->getLocalizedDescriptions();
        }
        if (null !== $command->getLocalizedMetaDescriptions()) {
            $supplier->meta_description = $command->getLocalizedMetaDescriptions();
        }
        if (null !== $command->getLocalizedMetaKeywords()) {
            $supplier->meta_keywords = $command->getLocalizedMetaKeywords();
        }
        if (null !== $command->getLocalizedMetaTitles()) {
            $supplier->meta_title = $command->getLocalizedMetaTitles();
        }
        if (null !== $command->isEnabled()) {
            $supplier->active = $command->isEnabled();
        }
        $supplier->date_upd = date('Y-m-d H:i:s');
    }

    /**
     * Populates Supplier address with given data
     *
     * @param Address $address
     * @param EditSupplierCommand $command
     */
    private function populateAddressWithData(Address $address, EditSupplierCommand $command)
    {
        if (null !== $command->getAddress()) {
            $address->address1 = $command->getAddress();
        }
        if (null !== $command->getAddress2()) {
            $address->address2 = $command->getAddress2();
        }
        if (null !== $command->getPostCode()) {
            $address->postcode = $command->getPostCode();
        }
        if (null !== $command->getPhone()) {
            $address->phone = $command->getPhone();
        }
        if (null !== $command->getMobilePhone()) {
            $address->phone_mobile = $command->getMobilePhone();
        }
        if (null !== $command->getCity()) {
            $address->city = $command->getCity();
        }
        if (null !== $command->getCountryId()) {
            $address->id_country = $command->getCountryId();
        }
        if (null !== $command->getStateId()) {
            $address->id_state = $command->getStateId();
        }
    }
}
