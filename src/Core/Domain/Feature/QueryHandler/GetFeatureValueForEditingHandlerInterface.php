<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureValueForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeatureValue;

interface GetFeatureValueForEditingHandlerInterface
{
    /**
     * @param GetFeatureValueForEditing $query
     *
     * @return EditableFeatureValue
     */
    public function handle(GetFeatureValueForEditing $query): EditableFeatureValue;
}
