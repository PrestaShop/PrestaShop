<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use RuntimeException;
use Supplier;

class SupplierFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given /^supplier "(.+)" with name "(.+)" exists$/
     *
     * @param string $reference
     * @param string $supplierName
     */
    public function existsByName(string $reference, string $supplierName): void
    {
        $id = (int) Supplier::getIdByName($supplierName);

        if (!$id) {
            throw new RuntimeException(sprintf('Supplier with name "%s" doesnt exist', $supplierName));
        }

        SharedStorage::getStorage()->set($reference, $id);
    }
}
