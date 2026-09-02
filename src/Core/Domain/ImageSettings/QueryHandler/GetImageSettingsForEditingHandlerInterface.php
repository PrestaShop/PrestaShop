<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageSettingsForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;

/**
 * Defines contract for GetImageSettingsForEditingHandlerInterface
 */
interface GetImageSettingsForEditingHandlerInterface
{
    /**
     * @param GetImageSettingsForEditing $query
     *
     * @return EditableImageSettings
     */
    public function handle(GetImageSettingsForEditing $query): EditableImageSettings;
}
