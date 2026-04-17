<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\Command;

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates title with provided data
 */
class AddTitleCommand
{
    /**
     * @var array<int, string>
     */
    protected $localizedNames;

    /**
     * @var Gender
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
     * @param array<string> $localizedNames
     * @param int $gender
     * @param UploadedFile|null $imgFile
     * @param int|null $imgWidth
     * @param int|null $imgHeight
     */
    public function __construct(
        array $localizedNames,
        int $gender,
        ?UploadedFile $imgFile = null,
        ?int $imgWidth = null,
        ?int $imgHeight = null
    ) {
        $this->localizedNames = $localizedNames;
        $this->gender = new Gender($gender);
        $this->imgFile = $imgFile;
        $this->imgWidth = $imgWidth;
        $this->imgHeight = $imgHeight;
    }

    /**
     * @return array<int, string>
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @return UploadedFile|null
     */
    public function getImageFile(): ?UploadedFile
    {
        return $this->imgFile;
    }

    /**
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        return $this->imgWidth;
    }

    /**
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        return $this->imgHeight;
    }
}
