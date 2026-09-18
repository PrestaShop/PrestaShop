<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;

final class StoreFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ShopContext $shopContext,
        private readonly CountryContext $countryContext,
    ) {
    }

    public function getData($storeId): array
    {
        /** @var StoreForEditing $store */
        $store = $this->queryBus->handle(new GetStoreForEditing((int) $storeId));

        return [
            'name' => $store->getLocalizedNames(),
            'address1' => $store->getLocalizedAddress1(),
            'address2' => $store->getLocalizedAddress2(),
            'postcode' => $store->getPostcode(),
            'city' => $store->getCity(),
            'id_country' => $store->getCountryId(),
            'id_state' => $store->getStateId() ?? 0,
            'latitude' => $store->getLatitude() !== null ? (string) $store->getLatitude() : '',
            'longitude' => $store->getLongitude() !== null ? (string) $store->getLongitude() : '',
            'phone' => $store->getPhone() ?? '',
            'fax' => $store->getFax() ?? '',
            'email' => $store->getEmail() ?? '',
            'note' => $store->getLocalizedNotes(),
            'active' => $store->isActive(),
            'image_preview' => $store->getImagePath(),
            'hours' => $this->formatHoursForForm($store->getLocalizedHours()),
            'shop_association' => $store->getShopAssociation(),
        ];
    }

    public function getDefaultData(): array
    {
        return [
            'active' => true,
            'id_country' => $this->countryContext->getId(),
            'shop_association' => $this->shopContext->getAssociatedShopIds(),
            'hours' => [],
        ];
    }

    /**
     * Converts hours per language (array of 7 strings) to the form format.
     * The StoreHoursType expects: {langId: {0: "09:00 | 18:00", 1: ..., 6: ...}}
     *
     * @param array<int, array<int, string>> $localizedHours
     *
     * @return array<int, array<int, string>>
     */
    private function formatHoursForForm(array $localizedHours): array
    {
        $result = [];
        foreach ($localizedHours as $langId => $days) {
            $result[$langId] = array_values($days);
        }

        return $result;
    }
}
