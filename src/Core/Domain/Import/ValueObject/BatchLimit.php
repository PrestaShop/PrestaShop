<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;

/**
 * Unit budget of one Continue call, or a job's default for it.
 *
 * Bounded above as well as below: a request-sized batch is the whole point of continuing a job
 * across requests, and a caller asking for the entire file in one call would only trade the bound
 * for a timeout.
 */
final class BatchLimit
{
    public const MIN_VALUE = 1;
    public const MAX_VALUE = 1000;

    /**
     * @throws ImportJobConstraintException
     */
    public function __construct(private readonly int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new ImportJobConstraintException(
                sprintf('Import batch limit must be between %d and %d units, %d given.', self::MIN_VALUE, self::MAX_VALUE, $value),
                ImportJobConstraintException::INVALID_BATCH_LIMIT
            );
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
