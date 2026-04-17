<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @var string
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
     * @var int|null
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
     * @return string
     */
    public function getPathName(): ?string
    {
        return $this->pathName;
    }

    /**
     * @param string $pathName
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
