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

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\CannotAddContactException;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\AddStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotAddStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShopException;
use Store;

/**
 * Class AddStoreHandler is used for adding store data.
 */
class AddStoreHandler extends AbstractObjectModelHandler implements AddStoreHandlerInterface
{
    /**
     * @throws CannotAddStoreException
     * @throws StoreException
     */
    public function handle(AddStoreCommand $command): StoreId
    {
        try {
            $entity = new Store();

            $entity->id_country = $command->getCountryId()->getValue();

            if (null !== $command->getStateId()) {
                $entity->id_state = $command->getStateId()->getValue();
            }

            $entity->postcode = $command->getPostcode()->getValue();
            $entity->city = $command->getCity()->getValue();

            if (null !== $command->getLatitude()) {
                $entity->latitude = $command->getLatitude()->getValue();
            }

            if (null !== $command->getLongitude()) {
                $entity->longitude = $command->getLongitude()->getValue();
            }

            if (null !== $command->getPhone()) {
                $entity->phone = $command->getPhone()->getValue();
            }

            if (null !== $command->getFax()) {
                $entity->fax = $command->getFax()->getValue();
            }

            if (null !== $command->getEmail()) {
                $entity->email = $command->getEmail()->getValue();
            }

            foreach ($command->getLocalizedNames() as $langId => $name) {
                $entity->name[$langId] = $name->getValue();
            }

            foreach ($command->getLocalizedAddress1() as $langId => $address) {
                $entity->address1[$langId] = $address->getValue();
            }

            foreach ($command->getLocalizedAddress2() as $langId => $address) {
                $entity->address2[$langId] = $address->getValue();
            }

            foreach ($command->getLocalizedHours() as $langId => $hours) {
                $entity->hours[$langId] = $hours->getValue();
            }

            foreach ($command->getLocalizedNotes() as $langId => $note) {
                $entity->note[$langId] = $note->getValue();
            }

            if (false === $entity->add()) {
                throw new CannotAddContactException('Unable to add contact');
            }

            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($entity, $command->getShopAssociation());
            }
        } catch (PrestaShopException $exception) {
            throw new StoreException('An unexpected error occurred when adding store', 0, $exception);
        }

        return new StoreId((int) $entity->id);
    }
}
