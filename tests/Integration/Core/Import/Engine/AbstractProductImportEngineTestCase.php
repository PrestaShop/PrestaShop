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

    protected function getProductIdByReference(string $reference): ?int
    {
        $productId = $this->fetchOne('SELECT id_product FROM {p}product WHERE reference = :reference', ['reference' => $reference]);

        return false === $productId ? null : (int) $productId;
    }
}
