<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;

/**
 * Identifies an import run.
 *
 * Unlike most domain identities (a positive int primary key), an import run is addressed by an
 * opaque UUID: the run is a transient token exposed in URLs and AJAX calls, so it must not leak
 * a sequential database id.
 */
final class ImportRunId
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    /**
     * @var string
     */
    private $value;

    /**
     * @throws ImportRunConstraintException
     */
    public function __construct(string $value)
    {
        if ('' === $value || !preg_match(self::UUID_PATTERN, $value)) {
            throw new ImportRunConstraintException(
                sprintf('Import run id "%s" is not a valid UUID.', $value),
                ImportRunConstraintException::INVALID_ID
            );
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
