<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds product customization data along with its custom fields data
 */
class Customization
{
    /**
     * @var int
     */
    private $customizationId;

    /**
     * @var CustomizationFieldData[]
     */
    private $customizationFieldsData;

    public function __construct(
        int $customizationId,
        array $customizationFieldsData
    ) {
        $this->customizationId = $customizationId;
        $this->customizationFieldsData = $customizationFieldsData;
    }

    /**
     * @return int
     */
    public function getCustomizationId(): int
    {
        return $this->customizationId;
    }

    /**
     * @return CustomizationFieldData[]
     */
    public function getCustomizationFieldsData(): array
    {
        return $this->customizationFieldsData;
    }
}
