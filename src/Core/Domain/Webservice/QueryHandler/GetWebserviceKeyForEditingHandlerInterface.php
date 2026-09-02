<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Query\GetWebserviceKeyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Webservice\QueryResult\EditableWebserviceKey;

/**
 * Interface for service that handles webservice key data retrieving for editing
 */
interface GetWebserviceKeyForEditingHandlerInterface
{
    /**
     * @param GetWebserviceKeyForEditing $query
     *
     * @return EditableWebserviceKey
     */
    public function handle(GetWebserviceKeyForEditing $query);
}
