<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Image\ImageValidator;
use PrestaShop\PrestaShop\Adapter\Image\Uploader\StoreImageUploader;
use PrestaShop\PrestaShop\Adapter\Store\HoursEncoder;
use PrestaShop\PrestaShop\Adapter\Store\Trait\StoreHandlerTrait;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\AddStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageFileNotFoundException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShopDatabaseException;
use Store;

#[AsCommandHandler]
final class AddStoreHandler implements AddStoreHandlerInterface
{
    use StoreHandlerTrait;

    public function __construct(
        private readonly StoreRepository $storeRepository,
        private readonly StoreImageUploader $imageUploader,
        private readonly ImageValidator $imageValidator,
        private readonly HoursEncoder $hoursEncoder,
    ) {
    }

    /**
     * @throws ImageUploadException
     * @throws ImageFileNotFoundException
     * @throws UploadedImageConstraintException
     * @throws PrestaShopDatabaseException
     * @throws StoreConstraintException
     */
    public function handle(AddStoreCommand $command): StoreId
    {
        $this->assertStateCountryConsistency(
            $command->getCountryId(),
            $command->getStateId()
        );

        if (null !== $command->getImagePath()) {
            $this->imageValidator->assertFileUploadLimits($command->getImagePath());
            $this->imageValidator->assertIsValidImageType($command->getImagePath());
        }

        $store = new Store();
        $store->id_country = $command->getCountryId();
        $store->id_state = $command->getStateId();
        $store->city = $command->getCity();
        $store->postcode = $command->getPostcode();
        $store->latitude = $command->getLatitude() !== null
            ? (float) $command->getLatitude()->round(8)
            : null;
        $store->longitude = $command->getLongitude() !== null
            ? (float) $command->getLongitude()->round(8)
            : null;
        $store->phone = $command->getPhone() ?? '';
        $store->fax = $command->getFax() ?? '';
        $store->email = $command->getEmail() ?? '';
        $store->active = $command->isActive();
        $store->name = $command->getLocalizedNames();
        $store->address1 = $command->getLocalizedAddress1();
        $store->address2 = $command->getLocalizedAddress2();
        $store->note = $command->getLocalizedNotes();
        $store->hours = $this->hoursEncoder->encode($command->getLocalizedHours());

        $storeId = $this->storeRepository->add($store);

        if (null !== $command->getShopAssociation()) {
            $this->storeRepository->updateShopAssociation($store, $command->getShopAssociation());
        }

        if (null !== $command->getImagePath()) {
            $this->imageUploader->upload($storeId->getValue(), $command->getImagePath());
        }

        return $storeId;
    }
}
