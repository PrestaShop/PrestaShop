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
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var int
     */
    private $fileSize;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @param $attachmentId
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException
     */
    public function __construct($attachmentId)
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
     */
    public function setAttachmentId(AttachmentId $attachmentId): void
    {
        $this->attachmentId = $attachmentId;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws AttachmentConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
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
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return $this
     */
    public function setLocalizedDescriptions(array $localizedDescriptions)
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }
}
