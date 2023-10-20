<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        } catch (PrestaShopException $e) {
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
        } catch (PrestaShopException $e) {
            throw new DeleteAttachmentException(sprintf('An error occurred when deleting Attachment object with id "%s".', $attachment->id));
        }
    }
}
