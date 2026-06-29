<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;

/**
 * Lifecycle status of an import run.
 */
final class ImportRunStatus
{
    public const PENDING = 'pending';
    public const RUNNING = 'running';
    public const FINISHED = 'finished';
    public const CANCELLED = 'cancelled';

    private const ALL = [
        self::PENDING,
        self::RUNNING,
        self::FINISHED,
        self::CANCELLED,
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @throws ImportRunConstraintException
     */
    public function __construct(string $value)
    {
        if (!in_array($value, self::ALL, true)) {
            throw new ImportRunConstraintException(
                sprintf('Import run status "%s" is not valid.', $value),
                ImportRunConstraintException::INVALID_STATUS
            );
        }

        $this->value = $value;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isFinished(): bool
    {
        return self::FINISHED === $this->value;
    }

    public function isCancelled(): bool
    {
        return self::CANCELLED === $this->value;
    }
}
