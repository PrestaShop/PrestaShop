<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;


use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

class EditAttachmentCommand
{
    /**
     * @var AttachmentId
     */
    private $attachmentId;

    /**
     * @var string|null
     */
    private $file;

    /**
     * @var string|null
     */
    private $fileName;

    /**
     * @var int|null
     */
    private $fileSize;

    /**
     * @var string|null
     */
    private $mimeType;

    /**
     * @var string[]|null
     */
    private $localizedNames;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

    /**
     * @param int $attachmentId
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException
     */
    public function __construct(int $attachmentId)
    {
        $this->attachmentId = new AttachmentId($attachmentId);
    }

    /**
     * @return AttachmentId
     */
    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
    }

    /**
     * @param AttachmentId $attachmentId
     * @return EditAttachmentCommand
     */
    public function setAttachmentId(AttachmentId $attachmentId): EditAttachmentCommand
    {
        $this->attachmentId = $attachmentId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     * @return EditAttachmentCommand
     */
    public function setFile(?string $file): EditAttachmentCommand
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     * @return EditAttachmentCommand
     */
    public function setFileName(?string $fileName): EditAttachmentCommand
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int|null $fileSize
     * @return EditAttachmentCommand
     */
    public function setFileSize(?int $fileSize): EditAttachmentCommand
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string|null $mimeType
     * @return EditAttachmentCommand
     */
    public function setMimeType(?string $mimeType): EditAttachmentCommand
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[]|null $localizedNames
     *
     * @return EditAttachmentCommand
     *
     * @throws AttachmentConstraintException
     */
    public function setLocalizedNames(?array $localizedNames): EditAttachmentCommand
    {
        if (empty($localizedNames)) {
            throw new AttachmentConstraintException(
                'Attachment name cannot be empty',
                AttachmentConstraintException::EMPTY_NAME
            );
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[]|null $localizedDescriptions
     * @return EditAttachmentCommand
     */
    public function setLocalizedDescriptions(?array $localizedDescriptions): EditAttachmentCommand
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }
}
