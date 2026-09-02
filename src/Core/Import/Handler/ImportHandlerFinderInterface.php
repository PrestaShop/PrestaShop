<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Handler;

/**
 * Interface ImportHandlerFinderInterface describes an import handler finder.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by \PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry
 */
interface ImportHandlerFinderInterface
{
    /**
     * Find the proper import handler for given entity type.
     *
     * @param int $importEntityType
     *
     * @return ImportHandlerInterface
     */
    public function find($importEntityType);
}
