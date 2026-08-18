<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;

/**
 * Product-flavored harness: binds the generic engine test case to the
 * ProductImporter and adds product-specific helpers.
 */
abstract class AbstractProductImportEngineTestCase extends AbstractImportEngineTestCase
{
    protected function getEntityImporter(): EntityImporterInterface
    {
        return $this->getProductImporter();
    }

    protected function getProductImporter(): ProductImporter
    {
        return self::getContainer()->get(ProductImporter::class);
    }

    /**
     * The LOWEST matching id, matching what the importer picks. product.reference
     * has no unique constraint, so the ORDER BY is what makes this deterministic —
     * without it MySQL row order would decide, and duplicate-reference assertions
     * would be meaningless.
     */
    protected function getProductIdByReference(string $reference): ?int
    {
        $productId = $this->fetchOne('SELECT id_product FROM {p}product WHERE reference = :reference ORDER BY id_product ASC LIMIT 1', ['reference' => $reference]);

        return false === $productId ? null : (int) $productId;
    }

    /**
     * Every product carrying the reference, lowest id first.
     *
     * @return list<int>
     */
    protected function getProductIdsByReference(string $reference): array
    {
        $rows = $this->fetchAll('SELECT id_product FROM {p}product WHERE reference = :reference ORDER BY id_product ASC', ['reference' => $reference]);

        return array_map(static fn (array $row) => (int) $row['id_product'], $rows);
    }
}
