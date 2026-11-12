<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;

final class CustomerB2bId
{
    /**
     * @throws CustomerB2bConstraintException
     */
    public function __construct(
        private readonly int $id,
    ) {
        $this->assertIsGreaterThanZero($id);
    }

    public function getValue(): int
    {
        return $this->id;
    }

    private function assertIsGreaterThanZero(int $id): void
    {
        if ($id > 0) {
            return;
        }

        throw new CustomerB2bConstraintException(\sprintf('B2B customer id %d is invalid.', $id), CustomerB2bConstraintException::INVALID_ID);
    }
}
