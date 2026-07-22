<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;

class BusinessEntityId
{
    private readonly int $id;

    public function __construct(
        int $businessEntityId
    ) {
        $this->assertIntegerIsGreaterThanZero($businessEntityId);
        $this->id = $businessEntityId;
    }

    public function getValue(): int
    {
        return $this->id;
    }

    private function assertIntegerIsGreaterThanZero(int $businessEntityId): void
    {
        if (0 >= $businessEntityId) {
            throw new BusinessEntityConstraintException(
                sprintf('Business entity id %s is invalid.', $businessEntityId),
                BusinessEntityConstraintException::INVALID_ID
            );
        }
    }
}
