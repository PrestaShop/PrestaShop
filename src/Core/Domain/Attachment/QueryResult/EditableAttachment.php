<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

use SplFileInfo;

/**
 * Stores editable data for attachment
 */
class EditableAttachment
{
    /**
     * @var int
     */
    private $attachmentId;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var string[]
     */
    private $description;

    /**
     * @var SplFileInfo|null
     */
    private $file;

    /**
     * @param int $attachmentId
     * @param string $fileName
     * @param string[] $name
     * @param string[] $description
     */
    public function __construct(
        int $attachmentId,
        string $fileName,
        array $name,
        array $description
    ) {
        $this->attachmentId = $attachmentId;
        $this->fileName = $fileName;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string[]
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return string[]|null
     */
    public function getDescription(): ?array
    {
        return $this->description;
    }

    /**
     * @return SplFileInfo|null
     */
    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }

    /**
     * @param SplFileInfo|null $file
     *
     * @return EditableAttachment
     */
    public function setFile(?SplFileInfo $file): EditableAttachment
    {
        $this->file = $file;

        return $this;
    }
}
