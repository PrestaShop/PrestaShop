<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult;

class Attribute
{
    /**
     * @var int
     */
    private $attributeId;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string[] key => value pairs where each key represents language id
     */
    private $localizedNames;

    /**
     * @var string|null
     */
    private $textureFilePath;

    /**
     * @param int $attributeId
     * @param int $position
     * @param string $color
     * @param string[] $localizedNames key => value pairs where each key represents language id
     */
    public function __construct(
        int $attributeId,
        int $position,
        string $color,
        array $localizedNames,
        ?string $textureFilePath = null
    ) {
        $this->attributeId = $attributeId;
        $this->position = $position;
        $this->color = $color;
        $this->localizedNames = $localizedNames;
        $this->textureFilePath = $textureFilePath;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getTextureFilePath(): ?string
    {
        return $this->textureFilePath;
    }
}
