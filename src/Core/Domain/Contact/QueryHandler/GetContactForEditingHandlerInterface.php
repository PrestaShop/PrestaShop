<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;

/**
 * Interface GetContactForEditingHandlerInterface defines contract for GetContactForEditingHandler
 */
interface GetContactForEditingHandlerInterface
{
    /**
     * @param GetContactForEditing $query
     *
     * @return EditableContact
     */
    public function handle(GetContactForEditing $query);
}
