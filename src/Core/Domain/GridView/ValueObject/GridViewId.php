<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;

class GridViewId
{
    /**
     * @param int $value
     *
     * @throws GridViewConstraintException
     */
    public function __construct(
        private readonly int $value,
    ) {
        if ($value <= 0) {
            throw new GridViewConstraintException(
                sprintf('Invalid grid view id "%d"', $value),
                GridViewConstraintException::INVALID_ID
            );
        }
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
