<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;

/**
 * Wipes an entity's data at the start of the database phase, mapping the engine's string entity
 * types to the legacy deleter's integers. Not open to modules, so the Start handler refuses
 * "truncate" for any type missing here.
 *
 * Destructive well beyond the entity: ~30 tables for products, image files on disk, and every shop
 * on multistore despite the job's single-shop scope — legacy parity, hence the super-admin gate.
 * $shopConstraint is unused for that reason, and kept so scoping it later touches no caller.
 */
final class ImportTruncator
{
    private const LEGACY_ENTITY_TYPES = [
        'product' => Entity::TYPE_PRODUCTS,
    ];

    public function __construct(
        private readonly ImportEntityDeleterInterface $entityDeleter,
    ) {
    }

    public function supports(string $entityType): bool
    {
        return array_key_exists($entityType, self::LEGACY_ENTITY_TYPES);
    }

    /**
     * @throws LogicException when the type is not truncatable; the Start handler rejects that
     *                        combination up front, so reaching this is a wiring mistake
     */
    public function truncate(string $entityType, ShopConstraint $shopConstraint): void
    {
        if (!$this->supports($entityType)) {
            throw new LogicException(sprintf('Import entity type "%s" cannot be truncated.', $entityType));
        }

        $this->entityDeleter->deleteAll(self::LEGACY_ENTITY_TYPES[$entityType]);
    }
}
