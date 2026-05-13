<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Store\Trait\StoreHandlerTrait;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\AddStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use Store;

#[AsCommandHandler]
final class AddStoreHandler implements AddStoreHandlerInterface
{
    use StoreHandlerTrait;

    public function __construct(
        private readonly StoreRepository $storeRepository,
    ) {
    }

    public function handle(AddStoreCommand $command): StoreId
    {
        $this->assertStateCountryConsistency(
            $command->getCountryId(),
            $command->getStateId()
        );

        $store = new Store();
        $store->id_country = $command->getCountryId();
        $store->id_state = $command->getStateId() ?? 0;
        $store->city = $command->getCity();
        $store->postcode = $command->getPostcode();
        // ObjectModel declares float but the DB column is DEFAULT NULL — @see PREFIX_store schema
        $store->latitude = $command->getLatitude() !== null  // @phpstan-ignore-line
            ? (float) number_format($command->getLatitude(), 8, '.', '')
            : null;
        $store->longitude = $command->getLongitude() !== null  // @phpstan-ignore-line
            ? (float) number_format($command->getLongitude(), 8, '.', '')
            : null;
        $store->phone = $command->getPhone() ?? '';
        $store->fax = $command->getFax() ?? '';
        $store->email = $command->getEmail() ?? '';
        $store->active = $command->isActive();
        $store->name = $command->getLocalizedNames();
        $store->address1 = $command->getLocalizedAddress1();
        $store->address2 = $command->getLocalizedAddress2();
        $store->note = $command->getLocalizedNotes();
        $store->hours = $this->encodeHours($command->getLocalizedHours());

        $storeId = $this->storeRepository->add($store);

        if (null !== $command->getShopAssociation()) {
            $this->associateWithShops($store, $command->getShopAssociation());
        }

        return $storeId;
    }

    /**
     * @param int[] $shopIds
     */
    private function associateWithShops(Store $store, array $shopIds): void
    {
        $store->associateTo($shopIds);
    }
}
