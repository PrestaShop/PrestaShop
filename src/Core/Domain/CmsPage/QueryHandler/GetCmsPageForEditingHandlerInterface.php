<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult\EditableCmsPage;

/**
 * Interface for service that handles getCmsPageForEditing query
 */
interface GetCmsPageForEditingHandlerInterface
{
    /**
     * @param GetCmsPageForEditing $query
     *
     * @return EditableCmsPage
     */
    public function handle(GetCmsPageForEditing $query);
}
