<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;

/**
 * Describes feature for editing handler.
 */
interface GetFeatureForEditingHandlerInterface
{
    /**
     * @param GetFeatureForEditing $query
     *
     * @return EditableFeature
     */
    public function handle(GetFeatureForEditing $query);
}
