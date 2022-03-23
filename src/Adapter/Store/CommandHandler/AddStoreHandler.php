<?php

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
final class AddStoreHandler extends AbstractObjectModelHandler implements AddStoreHandlerInterface
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
