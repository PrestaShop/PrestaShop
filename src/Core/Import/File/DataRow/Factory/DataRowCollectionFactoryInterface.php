<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory;

use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowCollectionInterface;
use SplFileInfo;

/**
 * Interface DataRowCollectionFactoryInterface describes a data row collection factory.
 *
 * @deprecated since 9.3, part of the legacy DataRow reading layer — the import
 * engine reads records as plain string arrays
 */
interface DataRowCollectionFactoryInterface
{
    /**
     * Builds a data row collection.
     *
     * @param SplFileInfo $file
     * @param int $maxRowsInCollection maximum number of rows this collection can have. Unlimited if not provided.
     *
     * @return DataRowCollectionInterface
     */
    public function buildFromFile(SplFileInfo $file, $maxRowsInCollection = null);
}
