<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

/**
 * Class AbstractBulkSupplierCommand is responsible for providing shared logic between all bulk actions
 * in brands and suppliers listing.
 */
abstract class AbstractBulkSupplierCommand
{
    /**
     * @param array $ids
     *
     * @return bool
     */
    protected function assertIsEmptyOrContainsNonIntegerValues(array $ids)
    {
        return empty($ids) || $ids !== array_filter($ids, 'is_int');
    }
}
