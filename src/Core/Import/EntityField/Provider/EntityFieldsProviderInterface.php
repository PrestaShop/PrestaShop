<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;

/**
 * Interface EntityFieldsProviderInterface defines a provider of entity fields.
 */
interface EntityFieldsProviderInterface
{
    /**
     * Get entity field as a collection.
     *
     * @return EntityFieldCollectionInterface
     */
    public function getCollection();
}
