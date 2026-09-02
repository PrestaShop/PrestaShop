<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\EditableExtraPropertyDefinition;

/**
 * Handler contract for GetExtraPropertyDefinitionForEditing.
 */
interface GetExtraPropertyDefinitionForEditingHandlerInterface
{
    /**
     * @param GetExtraPropertyDefinitionForEditing $query
     *
     * @return EditableExtraPropertyDefinition
     */
    public function handle(GetExtraPropertyDefinitionForEditing $query): EditableExtraPropertyDefinition;
}
