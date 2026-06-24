<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\EditStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class StoreFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data): int
    {
        $command = new AddStoreCommand(
            $data['name'],
            $data['address1'],
            (int) $data['id_country'],
            $data['city']
        );

        $command
            ->setLocalizedAddress2($data['address2'] ?? [])
            ->setStateId((int) $data['id_state'] ?: null)
            ->setPostcode($data['postcode'] ?? '')
            ->setLatitude($data['latitude'] !== '' && $data['latitude'] !== null ? new DecimalNumber($data['latitude']) : null)
            ->setLongitude($data['longitude'] !== '' && $data['longitude'] !== null ? new DecimalNumber($data['longitude']) : null)
            ->setPhone($data['phone'] ?: null)
            ->setFax($data['fax'] ?: null)
            ->setEmail($data['email'] ?: null)
            ->setActive((bool) ($data['active'] ?? true))
            ->setLocalizedNotes($data['note'] ?? [])
            ->setLocalizedHours($this->normalizeHours($data['hours'] ?? []))
            ->setImagePath($this->extractImagePath($data))
        ;

        if (isset($data['shop_association'])) {
            $command->setShopAssociation(
                is_array($data['shop_association']) ? $data['shop_association'] : []
            );
        }

        /** @var StoreId $storeId */
        $storeId = $this->commandBus->handle($command);

        return $storeId->getValue();
    }

    public function update($storeId, array $data): void
    {
        $command = (new EditStoreCommand((int) $storeId))
            ->setLocalizedNames($data['name'])
            ->setLocalizedAddress1($data['address1'])
            ->setLocalizedAddress2($data['address2'] ?? [])
            ->setCountryId((int) $data['id_country'])
            ->setStateId((int) $data['id_state'] ?: null)
            ->setCity($data['city'])
            ->setPostcode($data['postcode'] ?? '')
            ->setLatitude($data['latitude'] !== '' && $data['latitude'] !== null ? new DecimalNumber($data['latitude']) : null)
            ->setLongitude($data['longitude'] !== '' && $data['longitude'] !== null ? new DecimalNumber($data['longitude']) : null)
            ->setPhone($data['phone'] ?: null)
            ->setFax($data['fax'] ?: null)
            ->setEmail($data['email'] ?: null)
            ->setActive($data['active'])
            ->setLocalizedNotes($data['note'] ?? [])
            ->setLocalizedHours($this->normalizeHours($data['hours'] ?? []))
            ->setImagePath($this->extractImagePath($data))
        ;

        if (isset($data['shop_association'])) {
            $command->setShopAssociation(
                is_array($data['shop_association']) ? $data['shop_association'] : []
            );
        }

        $this->commandBus->handle($command);
    }

    private function extractImagePath(array $data): ?string
    {
        $uploadedImage = $data['image'] ?? null;

        return $uploadedImage instanceof UploadedFile ? $uploadedImage->getPathname() : null;
    }

    /**
     * Normalises hours from form compound type to command format.
     * Input: {langId: {0: "09:00 | 18:00", ...}}
     * Output: {langId: ["09:00 | 18:00", ...]}
     *
     * @param array<int|string, array<string, string>> $rawHours
     *
     * @return array<int, array<int, string>>
     */
    private function normalizeHours(array $rawHours): array
    {
        $result = [];
        foreach ($rawHours as $langId => $days) {
            if (is_array($days)) {
                $result[(int) $langId] = array_values($days);
            }
        }

        return $result;
    }
}
