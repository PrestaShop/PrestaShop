<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment;

use Attachment;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Abstract attachment handler
 */
abstract class AbstractAttachmentHandler
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $localizedTexts
     *
     * @throws AttachmentConstraintException
     */
    protected function assertHasDefaultLanguage(array $localizedTexts)
    {
        $errors = $this->validator->validate($localizedTexts, new DefaultLanguage());

        if (0 !== count($errors)) {
            throw new AttachmentConstraintException('Missing name in default language', AttachmentConstraintException::MISSING_NAME_IN_DEFAULT_LANGUAGE);
        }
    }

    /**
     * @param array $localizedDescription
     *
     * @throws AttachmentConstraintException
     */
    protected function assertDescriptionContainsCleanHtml(array $localizedDescription)
    {
        foreach ($localizedDescription as $description) {
            $errors = $this->validator->validate($description, new CleanHtml());

            if (0 !== count($errors)) {
                throw new AttachmentConstraintException(sprintf('Given description "%s" contains javascript events or script tags', $description), AttachmentConstraintException::INVALID_DESCRIPTION);
            }
        }
    }

    /**
     * @return string
     */
    protected function getUniqueFileName(): string
    {
        $uniqueFileName = sha1(uniqid());

        return $uniqueFileName;
    }

    /**
     * @param Attachment $attachment
     *
     * @throws AttachmentConstraintException
     * @throws PrestaShopException
     */
    protected function assertValidFields(Attachment $attachment)
    {
        if (!$attachment->validateFields(false) && !$attachment->validateFieldsLang(false)) {
            throw new AttachmentConstraintException('Attachment contains invalid field values', AttachmentConstraintException::INVALID_FIELDS);
        }
    }

    /**
     * @param AttachmentId $attachmentId
     *
     * @return Attachment
     *
     * @throws AttachmentNotFoundException
     */
    protected function getAttachment(AttachmentId $attachmentId): Attachment
    {
        $attachmentIdValue = $attachmentId->getValue();
        try {
            $attachment = new Attachment($attachmentIdValue);
        } catch (PrestaShopException) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentId->getValue()));
        }

        if ($attachment->id !== $attachmentId->getValue()) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentId->getValue()));
        }

        return $attachment;
    }

    /**
     * Deletes legacy Attachment
     *
     * @param Attachment $attachment
     *
     * @return bool
     *
     * @throws DeleteAttachmentException
     */
    protected function deleteAttachment(Attachment $attachment): bool
    {
        try {
            return $attachment->delete();
        } catch (PrestaShopException) {
            throw new DeleteAttachmentException(sprintf('An error occurred when deleting Attachment object with id "%s".', $attachment->id));
        }
    }
}
