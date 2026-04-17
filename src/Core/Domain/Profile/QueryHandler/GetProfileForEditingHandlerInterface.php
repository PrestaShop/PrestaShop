<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;

/**
 * Interface for service that gets Profile data for editing
 */
interface GetProfileForEditingHandlerInterface
{
    /**
     * @param GetProfileForEditing $query
     *
     * @return EditableProfile
     */
    public function handle(GetProfileForEditing $query): EditableProfile;
}
