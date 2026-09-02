<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds data of product customization field
 */
class ProductCustomizationField
{
    public const TYPE_FILE = 0;
    public const TYPE_TEXT = 1;

    /**
     * @var int
     */
    private $customizationFieldId;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isRequired;

    /**
     * @param int $customizationFieldId
     * @param int $type
     * @param string $name
     * @param bool $isRequired
     */
    public function __construct(
        int $customizationFieldId,
        int $type,
        string $name,
        bool $isRequired
    ) {
        $this->customizationFieldId = $customizationFieldId;
        $this->type = $type;
        $this->name = $name;
        $this->isRequired = $isRequired;
    }

    /**
     * @return int
     */
    public function getCustomizationFieldId(): int
    {
        return $this->customizationFieldId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
