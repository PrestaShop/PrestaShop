<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

class AttachmentInformation
{
    /**
     * @var int
     */
    private $attachmentId;

    /**
     * @var array<int, string>
     */
    private $localizedNames;

    /**
     * @var array<int, string>
     */
    private $localizedDescriptions;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $fileSize;

    /**
     * @param int $attachmentId
     * @param array $localizedNames
     * @param array $localizedDescriptions
     * @param string $fileName
     * @param string $mimeType
     * @param int $fileSize
     */
    public function __construct(
        int $attachmentId,
        array $localizedNames,
        array $localizedDescriptions,
        string $fileName,
        string $mimeType,
        int $fileSize
    ) {
        $this->attachmentId = $attachmentId;
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->fileSize = $fileSize;
    }

    /**
     * @return int
     */
    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }
}
