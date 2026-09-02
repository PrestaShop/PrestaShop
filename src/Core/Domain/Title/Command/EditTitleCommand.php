<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\Command;

use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Edits title with provided data
 */
class EditTitleCommand
{
    /**
     * @var TitleId
     */
    protected $titleId;

    /**
     * @var array<string>|null
     */
    protected $localizedNames;

    /**
     * @var Gender|null
     */
    protected $gender;

    /**
     * @var UploadedFile|null
     */
    protected $imgFile;

    /**
     * @var int|null
     */
    protected $imgWidth;

    /**
     * @var int|null
     */
    protected $imgHeight;

    /**
     * @param int $titleId
     *
     * @throws TitleConstraintException
     */
    public function __construct(int $titleId)
    {
        $this->titleId = new TitleId($titleId);
    }

    /**
     * @return TitleId
     */
    public function getTitleId(): TitleId
    {
        return $this->titleId;
    }

    /**
     * @return array<string>|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param array<string> $localizedNames
     *
     * @return self
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return Gender|null
     */
    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return self
     */
    public function setGender(int $gender): self
    {
        $this->gender = new Gender($gender);

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getImageFile(): ?UploadedFile
    {
        return $this->imgFile;
    }

    /**
     * @param UploadedFile $imageFile
     *
     * @return self
     */
    public function setImageFile(UploadedFile $imageFile): self
    {
        $this->imgFile = $imageFile;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        return $this->imgWidth;
    }

    /**
     * @param int|null $imageWidth
     *
     * @return self
     */
    public function setImageWidth(?int $imageWidth): self
    {
        $this->imgWidth = $imageWidth;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        return $this->imgHeight;
    }

    /**
     * @param int|null $imageHeight
     *
     * @return self
     */
    public function setImageHeight(?int $imageHeight): self
    {
        $this->imgHeight = $imageHeight;

        return $this;
    }
}
