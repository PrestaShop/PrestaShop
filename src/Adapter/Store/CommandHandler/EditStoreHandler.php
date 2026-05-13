<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Store\Trait\StoreHandlerTrait;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\EditStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\EditStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;

#[AsCommandHandler]
final class EditStoreHandler implements EditStoreHandlerInterface
{
    use StoreHandlerTrait;

    public function __construct(
        private readonly StoreRepository $storeRepository,
    ) {
    }

    public function handle(EditStoreCommand $command): void
    {
        $store = $this->storeRepository->get($command->getStoreId());

        if (null !== $command->getCountryId()) {
            $store->id_country = $command->getCountryId();
        }

        if ($command->isStateIdProvided()) {
            $store->id_state = $command->getStateId() ?? 0;
        }

        $this->assertStateCountryConsistency((int) $store->id_country, (int) $store->id_state ?: null);

        if (null !== $command->getCity()) {
            $store->city = $command->getCity();
        }
        if (null !== $command->getPostcode()) {
            $store->postcode = $command->getPostcode();
        }
        if (null !== $command->getLatitude()) {
            $store->latitude = (float) number_format($command->getLatitude(), 8, '.', '');
        }
        if (null !== $command->getLongitude()) {
            $store->longitude = (float) number_format($command->getLongitude(), 8, '.', '');
        }
        if (null !== $command->getPhone()) {
            $store->phone = $command->getPhone();
        }
        if (null !== $command->getFax()) {
            $store->fax = $command->getFax();
        }
        if (null !== $command->getEmail()) {
            $store->email = $command->getEmail();
        }
        if (null !== $command->getActive()) {
            $store->active = $command->getActive();
        }
        if (null !== $command->getLocalizedNames()) {
            $store->name = $command->getLocalizedNames();
        }
        if (null !== $command->getLocalizedAddress1()) {
            $store->address1 = $command->getLocalizedAddress1();
        }
        if (null !== $command->getLocalizedAddress2()) {
            $store->address2 = $command->getLocalizedAddress2();
        }
        if (null !== $command->getLocalizedNotes()) {
            $store->note = $command->getLocalizedNotes();
        }
        if (null !== $command->getLocalizedHours()) {
            $store->hours = $this->encodeHours($command->getLocalizedHours());
        }

        $this->storeRepository->update($store);

        if (null !== $command->getShopAssociation()) {
            $this->storeRepository->updateShopAssociation($store, $command->getShopAssociation());
        }
    }
}
