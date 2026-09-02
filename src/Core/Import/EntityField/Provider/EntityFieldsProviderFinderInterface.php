<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

/**
 * Interface EntityFieldsProviderFinderInterface describes an entity fields finder.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by \PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry
 */
interface EntityFieldsProviderFinderInterface
{
    /**
     * Find the appropriate entity fields provider.
     *
     * @param int $importEntity import entity ID (@see PrestaShop\PrestaShop\Core\Import\Entity)
     *
     * @return EntityFieldsProviderInterface
     */
    public function find($importEntity);
}
