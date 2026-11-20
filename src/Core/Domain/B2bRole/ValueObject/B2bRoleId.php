<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\B2bRole\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleConstraintException;

final class B2bRoleId
{
    /**
     * @throws B2bRoleConstraintException
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

        throw new B2bRoleConstraintException(\sprintf('B2B role id %d is invalid.', $id), B2bRoleConstraintException::INVALID_ID);
    }
}
