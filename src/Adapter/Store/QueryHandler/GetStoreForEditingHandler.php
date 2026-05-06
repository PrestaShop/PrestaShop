<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\QueryHandler;

use ImageManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryHandler\GetStoreForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use PrestaShopException;

#[AsQueryHandler]
class GetStoreForEditingHandler implements GetStoreForEditingHandlerInterface
{
    public function __construct(
        private readonly StoreRepository $storeRepository,
        private readonly ImageTagSourceParserInterface $imageTagSourceParser,
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

            $localizedHours = $this->decodeHours(is_array($store->hours) ? $store->hours : []);

            $shopAssociation = $store->getAssociatedShops();

            return new StoreForEditing(
                storeId: (int) $store->id,
                active: (bool) $store->active,
                localizedNames: $store->name,
                localizedAddress1: (array) $store->address1,
                localizedAddress2: is_array($store->address2) ? $store->address2 : [],
                localizedHours: $localizedHours,
                localizedNotes: is_array($store->note) ? $store->note : [],
                countryId: (int) $store->id_country,
                stateId: $store->id_state ? (int) $store->id_state : null,
                city: (string) $store->city,
                postcode: (string) $store->postcode,
                latitude: $store->latitude !== null && $store->latitude !== '' ? (float) $store->latitude : null,
                longitude: $store->longitude !== null && $store->longitude !== '' ? (float) $store->longitude : null,
                phone: $store->phone ?: null,
                fax: $store->fax ?: null,
                email: $store->email ?: null,
                storeImage: $this->getStoreImage((int) $store->id),
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

    private function getStoreImage(int $storeId): ?array
    {
        $pathToImage = _PS_STORE_IMG_DIR_ . $storeId . '.jpg';
        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'store_' . $storeId . '.jpg',
            350,
            'jpg',
            true,
            true
        );

        $imageSize = file_exists($pathToImage) ? filesize($pathToImage) / 1000 : '';

        if (empty($imageTag) || empty($imageSize)) {
            return null;
        }

        return [
            'path' => $this->imageTagSourceParser->parse($imageTag),
            'size' => sprintf('%1$.2f kB', $imageSize),
        ];
    }

    /**
     * Converts the raw JSON hours (per-lang array) to an array of 7 "HH:MM | HH:MM" strings per language.
     *
     * @param array<int, string> $rawHours keyed by lang id, each value is a JSON string
     *
     * @return array<int, array<int, string>> keyed by lang id, each value is an array of 7 day strings
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
                    // New format: ["09:00", "18:00"] → "09:00 | 18:00"
                    $open = trim($day[0]);
                    $close = trim($day[1]);
                    $days[] = ($open !== '' && $close !== '') ? $open . ' | ' . $close : $open;
                } elseif (is_array($day) && 1 === count($day)) {
                    // Legacy format: ["09:00AM - 07:00PM"] → use as-is
                    $days[] = trim($day[0]);
                } else {
                    $days[] = '';
                }
            }
            $result[(int) $langId] = $days;
        }

        return $result;
    }
}
