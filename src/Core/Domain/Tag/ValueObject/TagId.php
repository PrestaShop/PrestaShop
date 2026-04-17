<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagConstraintException;

class TagId
{
    private int $tagId;

    public function __construct(int $tagId)
    {
        $this->assertIsIntegerGreaterThanZero($tagId);
        $this->tagId = $tagId;
    }

    public function getValue(): int
    {
        return $this->tagId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws TagConstraintException
     */
    private function assertIsIntegerGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new TagConstraintException(sprintf('Invalid tag id "%s".', var_export($value, true)), TagConstraintException::INVALID_ID);
        }
    }
}
