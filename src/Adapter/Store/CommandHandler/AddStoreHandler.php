<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\AddStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use Store;

#[AsCommandHandler]
final class AddStoreHandler implements AddStoreHandlerInterface
{
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
        $store->latitude = $command->getLatitude() !== null
            ? number_format($command->getLatitude(), 8, '.', '')
            : null;
        $store->longitude = $command->getLongitude() !== null
            ? number_format($command->getLongitude(), 8, '.', '')
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

    private function assertStateCountryConsistency(int $countryId, ?int $stateId): void
    {
        $country = new Country($countryId);

        if ($country->contains_states && !$stateId) {
            throw new StoreConstraintException(
                'A state is required for the selected country.',
                StoreConstraintException::INVALID_STATE
            );
        }

        if (!$country->contains_states && $stateId) {
            throw new StoreConstraintException(
                'The selected country does not contain states.',
                StoreConstraintException::STATE_COUNTRY_MISMATCH
            );
        }
    }

    /**
     * Encodes the localised hours array back to JSON strings for storage.
     * Input: [$langId => ['09:00 | 18:00', '09:00 | 18:00', ...]] (7 items per lang)
     * Output: [$langId => '[[\"09:00\",\"18:00\"],...]']
     *
     * @param array<int, array<int, string>> $localizedHours
     *
     * @return array<int, string>
     */
    private function encodeHours(array $localizedHours): array
    {
        $result = [];
        foreach ($localizedHours as $langId => $days) {
            $encoded = [];
            foreach ($days as $day) {
                $parts = array_map('trim', explode('|', (string) $day, 2));
                $encoded[] = isset($parts[1]) ? [$parts[0], $parts[1]] : [$parts[0]];
            }
            $result[(int) $langId] = json_encode($encoded);
        }

        return $result;
    }
}
