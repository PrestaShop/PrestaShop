<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\QueryHandler;

use ImageManager;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Store\HoursEncoder;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
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
        private readonly HoursEncoder $hoursEncoder,
    ) {
    }

    public function handle(GetStoreForEditing $query): StoreForEditing
    {
        try {
            $store = $this->storeRepository->get($query->getStoreId());

            $localizedHours = $this->hoursEncoder->decode(is_array($store->hours) ? $store->hours : []);

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
                stateId: $store->id_state ?: null,
                city: (string) $store->city,
                postcode: (string) $store->postcode,
                latitude: $store->latitude !== null && $store->latitude !== '' ? new DecimalNumber((string) $store->latitude) : null,
                longitude: $store->longitude !== null && $store->longitude !== '' ? new DecimalNumber((string) $store->longitude) : null,
                phone: $store->phone ?: null,
                fax: $store->fax ?: null,
                email: $store->email ?: null,
                imagePath: $this->getImagePath((int) $store->id),
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

    private function getImagePath(int $storeId): ?string
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

        if (empty($imageTag)) {
            return null;
        }

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
