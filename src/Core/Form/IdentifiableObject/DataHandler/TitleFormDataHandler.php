<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\AddTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\EditTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted title form data
 */
class TitleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var UploadedFile|null $uploadedImage */
        $uploadedImage = $data['image'];

        /** @var TitleId $titleId */
        $titleId = $this->commandBus->handle(new AddTitleCommand(
            $data['name'],
            (int) $data['gender_type'],
            $uploadedImage,
            $data['img_width'],
            $data['img_height']
        ));

        return $titleId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = new EditTitleCommand((int) $id);
        $command
            ->setLocalizedNames($data['name'])
            ->setGender((int) $data['gender_type']);

        /** @var UploadedFile|null $uploadedImage */
        $uploadedImage = $data['image'];
        if ($uploadedImage instanceof UploadedFile) {
            $command
                ->setImageFile($uploadedImage)
                ->setImageWidth($data['img_width'])
                ->setImageHeight($data['img_height']);
        }

        $this->commandBus->handle($command);
    }
}
