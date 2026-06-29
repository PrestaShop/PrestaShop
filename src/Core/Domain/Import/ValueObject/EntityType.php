<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Import\Entity;

/**
 * The type of entity targeted by an import run (products, categories, …).
 *
 * Wraps the legacy {@see Entity} type constants so the domain carries a validated value.
 */
final class EntityType
{
    /**
     * @var int
     */
    private $value;

    /**
     * @throws ImportRunConstraintException
     */
    public function __construct(int $value)
    {
        if (!in_array($value, Entity::AVAILABLE_TYPES, true)) {
            throw new ImportRunConstraintException(
                sprintf('Import entity type "%d" is not supported.', $value),
                ImportRunConstraintException::INVALID_ENTITY_TYPE
            );
        }

        $this->value = $value;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public static function fromName(string $name): self
    {
        if (!array_key_exists($name, Entity::AVAILABLE_TYPES)) {
            throw new ImportRunConstraintException(
                sprintf('Import entity type with name "%s" is not supported.', $name),
                ImportRunConstraintException::INVALID_ENTITY_TYPE
            );
        }

        return new self(Entity::AVAILABLE_TYPES[$name]);
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
