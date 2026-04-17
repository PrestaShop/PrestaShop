<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;

/**
 * Holds virtual product file identification value
 */
class VirtualProductFileId
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $virtualProductFileId
     */
    public function __construct(int $virtualProductFileId)
    {
        $this->assertValueIsGreaterThanZero($virtualProductFileId);
        $this->value = $virtualProductFileId;
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
     * @throws VirtualProductFileConstraintException
     */
    private function assertValueIsGreaterThanZero(int $value): void
    {
        if (0 > $value) {
            throw new VirtualProductFileConstraintException(
                sprintf('Invalid virtual product file id "%d" value', $value),
                VirtualProductFileConstraintException::INVALID_ID
            );
        }
    }
}
