<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates/updates attachment from data submitted in category form
 */
final class AttachmentFormDataHandler implements FormDataHandlerInterface
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
    public function create(array $data)
    {
        $command = $this->createAddAttachmentCommand($data);

        /** @var AttachmentId $attachmentId */
        $attachmentId = $this->commandBus->handle($command);

        return $attachmentId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     */
    public function update($id, array $data)
    {
        $attachmentIdObject = new AttachmentId((int) $id);

        $command = $this->createEditAttachmentCommand($attachmentIdObject, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Creates edit attachment command from
     *
     * @param AttachmentId $attachmentId
     * @param array $data
     *
     * @return EditAttachmentCommand
     */
    private function createEditAttachmentCommand(
        AttachmentId $attachmentId,
        array $data
    ): EditAttachmentCommand {
        /** @var UploadedFile|null $fileObject */
        $fileObject = $data['file'];

        $command = new EditAttachmentCommand($attachmentId);
        $command->setLocalizedNames($data['name']);
        $command->setLocalizedDescriptions($data['file_description']);

        if ($fileObject instanceof UploadedFile) {
            $command->setFileInfo(
                $fileObject->getPathname(),
                $fileObject->getMimeType(),
                $fileObject->getClientOriginalName(),
                $fileObject->getSize()
            );
        }

        return $command;
    }

    /**
     * @param array $data
     *
     * @return AddAttachmentCommand
     */
    private function createAddAttachmentCommand(array $data)
    {
        $addAttachmentCommand = new AddAttachmentCommand(
            $data['name'],
            $data['file_description']
        );

        if (isset($data['file']) && $data['file'] !== null) {
            /** @var UploadedFile $file */
            $file = $data['file'];

            $addAttachmentCommand->setFileInformation(
                $file->getPathname(),
                $file->getSize(),
                $file->getMimeType(),
                $file->getClientOriginalName()
            );
        }

        return $addAttachmentCommand;
    }
}
