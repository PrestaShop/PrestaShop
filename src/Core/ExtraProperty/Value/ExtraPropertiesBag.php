<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IteratorAggregate;
use JsonSerializable;
use ObjectModelCore;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;
use Traversable;

/**
 * Lazy-loading grouped value bag for extra properties on an ObjectModel instance.
 *
 * Keys are module names (e.g. 'demoextrafield', '_core'). Values are ModuleFieldsBag
 * instances keyed by field name. Data is loaded from the DB on first access.
 *
 * Usage:
 *   $product->extra_properties['demoextrafield']['date_last_seen']         // read
 *   $product->extra_properties['demoextrafield']['date_last_seen'] = $val  // write + mark dirty
 *   foreach ($product->extra_properties as $module => $fields) { ... }    // iterate (triggers load)
 *   json_encode($product->extra_properties)                                // serialize
 */
final class ExtraPropertiesBag implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    private bool $loaded = false;
    /** @var array<string, ModuleFieldsBag> */
    private array $values = [];

    public function __construct(private readonly Closure $loader)
    {
    }

    /**
     * Builds a bag whose loader reads extra property values for one entity row.
     *
     * The container is resolved by the caller (e.g. ObjectModel::findContainer() in legacy
     * code, ContainerFinder in Adapter presenters) so this namespace stays free of legacy
     * Context lookups. All guards live inside the loader closure: construction is cheap,
     * never throws, and any invalid state resolves to an empty bag on first access.
     *
     * @param ContainerInterface|null $container Null = no-op bag (container unavailable)
     * @param class-string<ObjectModelCore> $objectModelClassName ObjectModelCore is the most-base
     *                                                            class common to all object models
     *                                                            (ObjectModel is generated dynamically
     *                                                            by the class override mechanism)
     * @param int $entityId Entity row id; <= 0 = no-op bag (not persisted yet)
     * @param int|null $langId Null fetches all languages (lang-keyed arrays), as used by ObjectModel/BO
     * @param ShopConstraint $shopConstraint Shop context — determines which row to read
     * @param bool $forFrontOffice When true (default, consistent with ExtraPropertyDefinition::$displayFront),
     *                             only display_front definitions are read; BO callers pass false
     */
    public static function createForEntity(
        ?ContainerInterface $container,
        string $objectModelClassName,
        int $entityId,
        ?int $langId,
        ShopConstraint $shopConstraint,
        bool $forFrontOffice = true,
    ): self {
        return new self(static function () use ($container, $objectModelClassName, $entityId, $langId, $shopConstraint, $forFrontOffice): array {
            if (null === $container || $entityId <= 0 || !is_subclass_of($objectModelClassName, ObjectModelCore::class)) {
                return [];
            }

            $def = ObjectModelCore::getDefinition($objectModelClassName);
            if (!is_array($def) || empty($def['table']) || empty($def['primary'])) {
                return [];
            }

            try {
                /** @var ExtraPropertyReaderInterface $reader */
                $reader = $container->get(ExtraPropertyReaderInterface::class);
                /** @var ExtraPropertyDefinitionRepositoryInterface $repository */
                $repository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
            } catch (Throwable) {
                // Legacy FO container may not expose the extra property services.
                return [];
            }

            $definitions = $repository->getAllDefinitions()->filterByEntity((string) $def['table']);
            if ($forFrontOffice) {
                $definitions = $definitions->filterForFrontOffice();
            }
            // X2: skip the DB read entirely when no matching fields are registered.
            if ($definitions->isEmpty()) {
                return [];
            }

            return $reader->getExtraProperties(
                (string) $def['table'],
                (string) $def['primary'],
                $entityId,
                $langId,
                $shopConstraint,
                ObjectModelCore::isClassLangMultishop($objectModelClassName),
                $definitions
            );
        });
    }

    private function ensureLoaded(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        /** @var array<string, array<string, mixed>> $grouped */
        $grouped = ($this->loader)();
        foreach ($grouped as $moduleKey => $fields) {
            $this->values[(string) $moduleKey] = new ModuleFieldsBag((array) $fields);
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->ensureLoaded();

        return isset($this->values[$offset]);
    }

    /**
     * Returns the ModuleFieldsBag for the given module key, auto-creating an empty one if unknown.
     * This allows chained writes: $bag['module']['field'] = value.
     */
    public function offsetGet(mixed $offset): ModuleFieldsBag
    {
        $this->ensureLoaded();
        if (!isset($this->values[$offset])) {
            $this->values[$offset] = new ModuleFieldsBag();
        }

        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // Assigning a full ModuleFieldsBag replaces the module bag.
        // Normal usage is chained: $bag['module']['field'] = value (goes through offsetGet + ModuleFieldsBag::offsetSet).
        $this->ensureLoaded();
        if ($value instanceof ModuleFieldsBag) {
            $this->values[(string) $offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->ensureLoaded();
        unset($this->values[$offset]);
    }

    public function getIterator(): Traversable
    {
        $this->ensureLoaded();

        return new ArrayIterator($this->values);
    }

    /**
     * Flattened representation: nested ModuleFieldsBag instances are unwrapped so the
     * result is plain data, usable directly without relying on json_encode() recursion.
     *
     * @return array<string, array<string, mixed>> [moduleKey => [propertyName => value]]
     */
    public function jsonSerialize(): array
    {
        $this->ensureLoaded();

        return array_map(
            static fn (ModuleFieldsBag $moduleBag): array => $moduleBag->jsonSerialize(),
            $this->values
        );
    }

    public function hasModifications(): bool
    {
        foreach ($this->values as $moduleBag) {
            if ($moduleBag->hasModifications()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dirty fields grouped by module — the same shape the reader returns and the writer accepts.
     *
     * @return array<string, array<string, mixed>> [moduleKey => [propertyName => value]]
     */
    public function getModifiedValues(): array
    {
        $grouped = [];
        foreach ($this->values as $moduleKey => $moduleBag) {
            $modified = $moduleBag->getModifiedValues();
            if ([] !== $modified) {
                $grouped[$moduleKey] = $modified;
            }
        }

        return $grouped;
    }
}
