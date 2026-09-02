<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;

/**
 * Defines contract for GetImageTypeForEditingHandlerInterface
 */
interface GetImageTypeForEditingHandlerInterface
{
    /**
     * @param GetImageTypeForEditing $query
     *
     * @return EditableImageType
     */
    public function handle(GetImageTypeForEditing $query): EditableImageType;
}
