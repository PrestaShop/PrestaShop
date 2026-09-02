<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;

/**
 * Holds product customization identification data
 */
class CustomizationId
{
    /**
     * @var int
     */
    private $customizationId;

    /**
     * @param int $customizationId
     */
    public function __construct(int $customizationId)
    {
        $this->assertValueIsPositive($customizationId);
        $this->customizationId = $customizationId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->customizationId;
    }

    private function assertValueIsPositive(int $value): void
    {
        if (0 >= $value) {
            throw new CustomizationConstraintException(
                sprintf('Customization id must be positive integer. "%s" given', $value),
                CustomizationConstraintException::INVALID_ID
            );
        }
    }
}
