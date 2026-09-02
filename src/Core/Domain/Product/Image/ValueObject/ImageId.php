<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageConstraintException;

/**
 * Holds image identification
 */
class ImageId
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->assertIntegerIsGreaterThanZero($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws ProductImageConstraintException
     */
    private function assertIntegerIsGreaterThanZero(int $value): void
    {
        if ($value <= 0) {
            throw new ProductImageConstraintException(
                sprintf('Invalid image id "%d"', $value),
                ProductImageConstraintException::INVALID_ID
            );
        }
    }
}
