<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\AttachmentFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\AddAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotAddAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptyFileException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handles attachment creation and file uploading procedures
 */
#[AsCommandHandler]
final class AddAttachmentHandler extends AbstractAttachmentHandler implements AddAttachmentHandlerInterface
{
    /**
     * @var AttachmentFileUploaderInterface
     */
    protected $fileUploader;

    /**
     * @param ValidatorInterface $validator
     * @param AttachmentFileUploaderInterface $fileUploader
     */
    public function __construct(ValidatorInterface $validator, AttachmentFileUploaderInterface $fileUploader)
    {
        parent::__construct($validator);

        $this->fileUploader = $fileUploader;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws AttachmentNotFoundException
     */
    public function handle(AddAttachmentCommand $command): AttachmentId
    {
        try {
            if ($command->getFilePathName() === null) {
                throw new EmptyFileException('No file found to be uploaded');
            }

            $attachment = new Attachment();

            $this->assertDescriptionContainsCleanHtml($command->getLocalizedDescriptions());
            $this->assertHasDefaultLanguage($command->getLocalizedNames());

            $uniqueFileName = $this->getUniqueFileName();

            $attachment->description = $command->getLocalizedDescriptions();
            $attachment->name = $command->getLocalizedNames();
            $attachment->file_name = $command->getOriginalName();
            $attachment->file = $uniqueFileName;
            $attachment->mime = $command->getMimeType();
            $attachment->file_size = $command->getFileSize();

            $this->assertValidFields($attachment);

            $this->fileUploader->upload($command->getFilePathName(), $uniqueFileName, $command->getFileSize());

            if (false === $attachment->add()) {
                throw new CannotAddAttachmentException('Failed to add attachment');
            }
        } catch (PrestaShopException $e) {
            throw new AttachmentException('An unexpected error occurred when adding attachment', 0, $e);
        }

        return new AttachmentId($attachment->id);
    }
}
