<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Adapter\Image\Uploader\ManufacturerImageUploader;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted manufacturer form data
 */
final class ManufacturerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;
    /**
     * @var ManufacturerImageUploader
     */
    private $imageUploader;

    /**
     * @param CommandBusInterface $bus
     * @param ManufacturerImageUploader $imageUploader
     */
    public function __construct(
        CommandBusInterface $bus,
        ManufacturerImageUploader $imageUploader
    ) {
        $this->bus = $bus;
        $this->imageUploader = $imageUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['shop_association']) || !$data['shop_association']) {
            $data['shop_association'] = [];
        }

        /** @var UploadedFile $uploadedLogo */
        $uploadedLogo = $data['logo'];

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->checkImageIsAllowedForUpload($uploadedLogo);
        }

        /** @var ManufacturerId $manufacturerId */
        $manufacturerId = $this->bus->handle(new AddManufacturerCommand(
            $data['name'],
            $data['is_enabled'],
            $data['short_description'],
            $data['description'],
            $data['meta_title'],
            $data['meta_description'],
            $data['shop_association']
        ));

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($manufacturerId->getValue(), $uploadedLogo);
        }

        return $manufacturerId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($manufacturerId, array $data)
    {
        /** @var UploadedFile $uploadedLogo */
        $uploadedLogo = $data['logo'];

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($manufacturerId, $uploadedLogo);
        }

        $command = (new EditManufacturerCommand($manufacturerId))
            ->setName((string) $data['name'])
            ->setLocalizedShortDescriptions($data['short_description'])
            ->setLocalizedDescriptions($data['description'])
            ->setLocalizedMetaDescriptions($data['meta_description'])
            ->setLocalizedMetaTitles($data['meta_title'])
            ->setEnabled((bool) $data['is_enabled'])
        ;

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation);

            $command->setAssociatedShops($shopAssociation);
        }

        $this->bus->handle($command);
    }
}
