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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

/**
 * Attachment editing command
 */
class EditAttachmentCommand
{
    /**
     * @var AttachmentId
     */
    private $attachmentId;

    /**
     * @var string|null
     */
    private $pathName;

    /**
     * @var string|null
     */
    private $originalFileName;

    /**
     * @var string|null
     */
    private $mimeType;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

    /**
     * @var string|null
     */
    private $fileSize;

    /**
     * @param AttachmentId $attachmentId
     */
    public function __construct(AttachmentId $attachmentId)
    {
        $this->attachmentId = $attachmentId;
    }

    /**
     * @return AttachmentId
     */
    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
    }

    /**
     * @return string|null
     */
    public function getPathName(): ?string
    {
        return $this->pathName;
    }

    /**
     * @param string|null $pathName
     * @param string $mimeType
     * @param string $originalFileName
     * @param int $fileSize
     *
     * @return EditAttachmentCommand
     */
    public function setFileInfo(
        string $pathName,
        string $mimeType,
        string $originalFileName,
        int $fileSize
    ): EditAttachmentCommand {
        $this->pathName = $pathName;
        $this->mimeType = $mimeType;
        $this->originalFileName = $originalFileName;
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
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
     * @return EditAttachmentCommand
     */
    public function setLocalizedNames(array $localizedNames): EditAttachmentCommand
    {
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
     *
     * @return EditAttachmentCommand
     */
    public function setLocalizedDescriptions(?array $localizedDescriptions): EditAttachmentCommand
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }
}
