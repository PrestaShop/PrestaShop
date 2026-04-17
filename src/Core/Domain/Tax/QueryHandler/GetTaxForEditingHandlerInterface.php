<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult\EditableTax;

/**
 * Defines contract for service that gets tax for editing
 */
interface GetTaxForEditingHandlerInterface
{
    /**
     * @param GetTaxForEditing $query
     *
     * @return EditableTax
     */
    public function handle(GetTaxForEditing $query);
}
