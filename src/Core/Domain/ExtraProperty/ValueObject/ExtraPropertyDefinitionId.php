<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyConstraintException;

/**
 * Encapsulates and validates an extra property definition primary key.
 */
class ExtraPropertyDefinitionId
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @param int $id
     *
     * @throws ExtraPropertyConstraintException
     */
    public function __construct(int $id)
    {
        $this->assertIsGreaterThanZero($id);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @throws ExtraPropertyConstraintException
     */
    protected function assertIsGreaterThanZero(int $id): void
    {
        if ($id <= 0) {
            throw new ExtraPropertyConstraintException(
                sprintf('Extra property definition id must be greater than 0, %d given.', $id),
                ExtraPropertyConstraintException::INVALID_ID
            );
        }
    }
}
