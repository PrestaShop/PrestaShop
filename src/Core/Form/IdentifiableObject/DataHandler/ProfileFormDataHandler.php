<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\AddProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\EditProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Saves or updates Profile using form data
 */
final class ProfileFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @param CommandBusInterface $bus
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(
        CommandBusInterface $bus,
        ImageUploaderInterface $imageUploader
    ) {
        $this->bus = $bus;
        $this->imageUploader = $imageUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        /** @var ProfileId $profileId */
        $profileId = $this->bus->handle(new AddProfileCommand($data['name']));

        /** @var UploadedFile $uploadedAvatar */
        $uploadedAvatar = $data['avatarUrl'] ?? null;
        if ($uploadedAvatar instanceof UploadedFile) {
            $this->imageUploader->upload($profileId->getValue(), $uploadedAvatar);
        }

        return $profileId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($profileId, array $data)
    {
        /** @var UploadedFile $uploadedAvatar */
        $uploadedAvatar = $data['avatarUrl'];
        if ($uploadedAvatar instanceof UploadedFile) {
            $this->imageUploader->upload($profileId, $uploadedAvatar);
        }

        /* @var ProfileId $profileId */
        $this->bus->handle(new EditProfileCommand($profileId, $data['name']));
    }
}
