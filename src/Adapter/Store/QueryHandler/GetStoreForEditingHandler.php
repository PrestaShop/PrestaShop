<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryHandler\GetStoreForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShopException;
use Shop;

#[AsQueryHandler]
class GetStoreForEditingHandler implements GetStoreForEditingHandlerInterface
{
    public function __construct(
        private readonly StoreRepository $storeRepository,
    ) {
    }

    public function handle(GetStoreForEditing $query): StoreForEditing
    {
        try {
            $store = $this->storeRepository->get($query->getStoreId());

            if (0 >= $store->id) {
                throw new StoreNotFoundException(
                    sprintf('Store with id %d was not found', $query->getStoreId()->getValue())
                );
            }

            $localizedHours = $this->decodeHours($store->hours ?? []);

            $shopAssociation = array_map(
                static fn (string $id): int => (int) $id,
                $store->getAssociatedShops()
            );

            return new StoreForEditing(
                storeId: (int) $store->id,
                active: (bool) $store->active,
                localizedNames: $store->name ?? [],
                localizedAddress1: $store->address1 ?? [],
                localizedAddress2: $store->address2 ?? [],
                localizedHours: $localizedHours,
                localizedNotes: $store->note ?? [],
                countryId: (int) $store->id_country,
                stateId: $store->id_state ? (int) $store->id_state : null,
                city: (string) $store->city,
                postcode: (string) $store->postcode,
                latitude: $store->latitude !== null && $store->latitude !== '' ? (float) $store->latitude : null,
                longitude: $store->longitude !== null && $store->longitude !== '' ? (float) $store->longitude : null,
                phone: $store->phone ?: null,
                fax: $store->fax ?: null,
                email: $store->email ?: null,
                hasImage: (bool) $store->id_image,
                shopAssociation: $shopAssociation,
            );
        } catch (PrestaShopException $e) {
            throw new StoreException(
                sprintf('An unexpected error occurred when retrieving store %d', $query->getStoreId()->getValue()),
                0,
                $e
            );
        }
    }

    /**
     * Converts the raw JSON hours (per-lang array) to an array of 7 "HH:MM | HH:MM" strings per language.
     *
     * @param array<int, string> $rawHours  keyed by lang id, each value is a JSON string
     *
     * @return array<int, array<int, string>>  keyed by lang id, each value is an array of 7 day strings
     */
    private function decodeHours(array $rawHours): array
    {
        $result = [];
        foreach ($rawHours as $langId => $jsonString) {
            if (empty($jsonString)) {
                $result[(int) $langId] = array_fill(0, 7, '');
                continue;
            }
            $decoded = json_decode($jsonString, true);
            if (!is_array($decoded)) {
                $result[(int) $langId] = array_fill(0, 7, '');
                continue;
            }
            $days = [];
            foreach ($decoded as $day) {
                if (is_array($day) && 2 === count($day)) {
                    $open = trim($day[0]);
                    $close = trim($day[1]);
                    $days[] = ($open !== '' || $close !== '') ? $open . ' | ' . $close : '';
                } else {
                    $days[] = '';
                }
            }
            $result[(int) $langId] = $days;
        }

        return $result;
    }
}
