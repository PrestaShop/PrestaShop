<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Immutable, iterable collection of extra property definitions.
 *
 * Each item is a typed ExtraPropertyDefinition value object.
 * Provides fluent helpers for filtering and inspection without modifying the original data.
 *
 * @implements IteratorAggregate<int, ExtraPropertyDefinition>
 */
final class ExtraPropertyDefinitionCollection implements Countable, IteratorAggregate
{
    /** @var list<ExtraPropertyDefinition> */
    private readonly array $definitions;

    /** @var self|null Cached empty instance — avoids repeated allocations for entities without extra properties */
    private static ?self $emptyInstance = null;

    /**
     * @param list<ExtraPropertyDefinition> $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = array_values($definitions);
    }

    /**
     * Returns the shared empty collection instance (singleton).
     *
     * Reusing a single instance avoids repeated allocations in the common case
     * where an entity has no extra properties registered.
     */
    public static function empty(): self
    {
        if (null === self::$emptyInstance) {
            self::$emptyInstance = new self([]);
        }

        return self::$emptyInstance;
    }

    // -------------------------------------------------------------------------
    // Countable / IteratorAggregate
    // -------------------------------------------------------------------------

    public function count(): int
    {
        return count($this->definitions);
    }

    /**
     * @return Traversable<int, ExtraPropertyDefinition>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->definitions);
    }

    // -------------------------------------------------------------------------
    // Inspection helpers
    // -------------------------------------------------------------------------

    public function isEmpty(): bool
    {
        return empty($this->definitions);
    }

    /**
     * Returns the first definition, or null when the collection is empty.
     *
     * @return ExtraPropertyDefinition|null
     */
    public function first(): ?ExtraPropertyDefinition
    {
        return $this->definitions[0] ?? null;
    }

    // -------------------------------------------------------------------------
    // Filtering (return new immutable instances)
    // -------------------------------------------------------------------------

    /**
     * Returns a new collection filtered to the given module.
     *
     * Pass null to get core (no-module) definitions.
     * Pass '_core' as a string alias for core fields.
     *
     * @param string|null $moduleName Module technical name, or null/'_core'/'' for core fields
     */
    public function filterByModuleName(?string $moduleName): self
    {
        // '_core', null and '' all refer to core fields (module_name IS NULL in DB).
        $isCore = null === $moduleName || '_core' === $moduleName || '' === $moduleName;
        $filtered = array_filter(
            $this->definitions,
            static function (ExtraPropertyDefinition $d) use ($isCore, $moduleName): bool {
                $defModule = $d->getModuleName();
                if ($isCore) {
                    return null === $defModule;
                }

                return $defModule === $moduleName;
            }
        );

        return new self(array_values($filtered));
    }

    /**
     * Returns a new collection filtered to the given scope.
     */
    public function filterByScope(ExtraPropertyScope $scope): self
    {
        $filtered = array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => $d->getScope() === $scope
        );

        return new self(array_values($filtered));
    }

    /**
     * Returns a new collection filtered to the given entity.
     *
     * Useful when a collection groups definitions from multiple entities.
     *
     * @param string $entityName Entity table name (e.g. 'product')
     */
    public function filterByEntity(string $entityName): self
    {
        return new self(array_values(array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => $d->getEntityName() === $entityName
        )));
    }

    /**
     * Returns a new collection containing only definitions associated with the given form ID.
     *
     * @param string $formId Form identifier (usually equals form block_prefix, e.g. 'category')
     */
    public function filterByForm(string $formId): self
    {
        return new self(array_values(array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => null !== $d->getFormEntry($formId)
        )));
    }

    /**
     * Returns a new collection containing only definitions associated with the given grid ID.
     *
     * A definition is included when any of its associated_grids entries targets $gridId,
     * using the "gridId[.columnId[:before|after]]" format.
     *
     * @param string $gridId Grid identifier (e.g. 'product', 'customer')
     */
    public function filterByGrid(string $gridId): self
    {
        return new self(array_values(array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => null !== $d->getGridEntry($gridId)
        )));
    }

    /**
     * Filters definitions eligible for front-office display.
     *
     * Only fields with display_front = true are returned.
     * Use this before passing definitions to the FO reader or presenter.
     */
    public function filterForFrontOffice(): self
    {
        return new self(array_values(array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => $d->isDisplayFront()
        )));
    }

    /**
     * Returns a new collection containing only definitions that target the given Admin API operation,
     * identified by its URI template and HTTP method (via ExtraPropertyDefinition::matchesApi()).
     *
     * Chainable: $collection->filterByEntity('product')->filterByApi('/products/{productId}', 'GET')
     */
    public function filterByApi(string $uriTemplate, string $method): self
    {
        return new self(array_values(array_filter(
            $this->definitions,
            static fn (ExtraPropertyDefinition $d): bool => $d->matchesApi($uriTemplate, $method)
        )));
    }
}
