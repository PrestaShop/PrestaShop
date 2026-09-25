<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;

/**
 * The entity an import job targets, as declared by its importer (e.g. "product", "demo_note").
 *
 * Shape only. Whether an importer is registered for it is checked by the Start handler: that
 * changes with the installed modules, and a job whose module was uninstalled must still load to
 * report why it cannot continue.
 */
final class EntityType
{
    /**
     * Matches the entity_type column.
     */
    public const MAX_LENGTH = 64;

    private const PATTERN = '/^[a-z][a-z0-9_]*$/';

    private readonly string $value;

    /**
     * @throws ImportJobConstraintException
     */
    public function __construct(string $value)
    {
        if (!preg_match(self::PATTERN, $value) || strlen($value) > self::MAX_LENGTH) {
            throw new ImportJobConstraintException(
                sprintf(
                    'Import entity type "%s" is invalid: expected lowercase snake_case, %d characters at most.',
                    $value,
                    self::MAX_LENGTH
                ),
                ImportJobConstraintException::INVALID_ENTITY_TYPE
            );
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
