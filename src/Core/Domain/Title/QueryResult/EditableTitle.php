<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\QueryResult;

/**
 * Transfers state data for editing
 */
class EditableTitle
{
    /**
     * @var int
     */
    protected $titleId;

    /**
     * @var array<string>
     */
    protected $localizedNames;

    /**
     * @var int
     */
    protected $gender;

    protected int $width;

    protected int $height;

    /**
     * @param int $titleId
     * @param array<string> $localizedNames
     * @param int $gender
     */
    public function __construct(
        int $titleId,
        array $localizedNames,
        int $gender,
        int $width,
        int $height
    ) {
        $this->titleId = $titleId;
        $this->localizedNames = $localizedNames;
        $this->gender = $gender;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getTitleId(): int
    {
        return $this->titleId;
    }

    /**
     * @return array<string>
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
