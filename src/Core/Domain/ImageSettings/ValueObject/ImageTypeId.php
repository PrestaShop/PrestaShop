<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;

/**
 * Defines Image Type ID with it's constraints
 */
class ImageTypeId
{
    private int $imageTypeId;

    /**
     * @param int $imageTypeId
     *
     * @throws ImageTypeException
     */
    public function __construct(int $imageTypeId)
    {
        $this->assertIntegerIsGreaterThanZero($imageTypeId);
        $this->imageTypeId = $imageTypeId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->imageTypeId;
    }

    /**
     * @param int $imageTypeId
     *
     * @throws ImageTypeException
     */
    private function assertIntegerIsGreaterThanZero(int $imageTypeId): void
    {
        if (0 >= $imageTypeId) {
            throw new ImageTypeException(sprintf('Image type id %d is invalid. Image type id have to be number bigger than zero.', $imageTypeId));
        }
    }
}
