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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\EditAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotUpdateAttachmentException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handles editing of attachment and file uploading procedures
 */
#[AsCommandHandler]
final class EditAttachmentHandler extends AbstractAttachmentHandler implements EditAttachmentHandlerInterface
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
     * @throws CannotUpdateAttachmentException
     */
    public function handle(EditAttachmentCommand $command)
    {
        $attachmentIdValue = $command->getAttachmentId()->getValue();

        try {
            $attachment = new Attachment($attachmentIdValue);
        } catch (PrestaShopException) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        if ($attachment->id !== $attachmentIdValue) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        $this->updateAttachmentFromCommandData($attachment, $command);
    }

    /**
     * @param Attachment $attachment
     * @param EditAttachmentCommand $command
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws AttachmentNotFoundException
     * @throws CannotUpdateAttachmentException
     */
    private function updateAttachmentFromCommandData(Attachment $attachment, EditAttachmentCommand $command)
    {
        try {
            if (!$attachment->validateFields(false) && !$attachment->validateFieldsLang(false)) {
                throw new AttachmentConstraintException('Attachment contains invalid field values', AttachmentConstraintException::INVALID_FIELDS);
            }

            $this->assertDescriptionContainsCleanHtml($command->getLocalizedDescriptions());
            $this->assertHasDefaultLanguage($command->getLocalizedNames());

            $attachment->description = $command->getLocalizedDescriptions();
            $attachment->name = $command->getLocalizedNames();

            $this->assertValidFields($attachment);

            if (null !== $command->getPathName()) {
                $uniqueFileName = $this->getUniqueFileName();

                $attachment->file_name = $command->getOriginalFileName();
                $attachment->file = $uniqueFileName;
                $attachment->mime = $command->getMimeType();

                $this->assertValidFields($attachment);

                $this->fileUploader->upload(
                    $command->getPathName(),
                    $uniqueFileName,
                    $command->getFileSize(),
                    $command->getAttachmentId()->getValue()
                );
            }

            if (false === $attachment->update()) {
                throw new CannotUpdateAttachmentException('Failed to update attachment');
            }
        } catch (PrestaShopException $e) {
            throw new AttachmentException('An unexpected error occurred when updating attachment', 0, $e);
        }
    }
}
