<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization;

/**
 * Transfers customization field data
 */
class CustomizationField
{
    /**
     * @var int
     */
    private $type;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $addedByModule;

    /**
     * @var int|null
     */
    private $customizationFieldId;

    /**
     * @param int $type
     * @param string[] $localizedNames
     * @param bool $required
     * @param bool $addedByModule
     * @param int|null $customizationFieldId If provided, means that its existing CustomizationField and should be updated
     */
    public function __construct(
        int $type,
        array $localizedNames,
        bool $required,
        bool $addedByModule = false,
        ?int $customizationFieldId = null
    ) {
        $this->type = $type;
        $this->localizedNames = $localizedNames;
        $this->required = $required;
        $this->addedByModule = $addedByModule;
        $this->customizationFieldId = $customizationFieldId ?? null;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isAddedByModule(): bool
    {
        return $this->addedByModule;
    }

    /**
     * @return int|null
     */
    public function getCustomizationFieldId(): ?int
    {
        return $this->customizationFieldId;
    }
}
