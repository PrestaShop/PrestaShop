<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

/**
 * Attachment creation command
 */
class AddAttachmentCommand
{
    /**
     * @var string|null
     */
    private $pathName;

    /**
     * @var int|null
     */
    private $fileSize;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedDescriptions = [];

    /**
     * @var string|null
     */
    private $mimeType;

    /**
     * @var string|null
     */
    private $originalName;

    /**
     * @param array $localizedNames
     * @param array $localizedDescriptions
     */
    public function __construct(
        array $localizedNames,
        array $localizedDescriptions
    ) {
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
    }

    /**
     * @param string $pathName
     * @param int $fileSize
     * @param string $mimeType
     * @param string $originalName
     */
    public function setFileInformation(
        string $pathName,
        int $fileSize,
        string $mimeType,
        string $originalName
    ): void {
        $this->pathName = $pathName;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->originalName = $originalName;
    }

    /**
     * @return string|null
     */
    public function getFilePathName(): ?string
    {
        return $this->pathName;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
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
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }
}
