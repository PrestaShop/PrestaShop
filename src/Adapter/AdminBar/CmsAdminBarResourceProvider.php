<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use CmsController;
use Throwable;

final class CmsAdminBarResourceProvider implements AdminBarResourceProviderInterface
{
    public function getResource(object $controller): ?AdminBarResource
    {
        if (!$controller instanceof CmsController) {
            return null;
        }

        try {
            $cms = $controller->getCms();
            if ($cms !== null && (int) $cms->id > 0) {
                return new AdminBarResource('cms', (int) $cms->id);
            }
            $category = $controller->getCmsCategory();
            $id = $category === null ? 0 : (int) $category->id;

            return $id > 0 ? new AdminBarResource('cms_category', $id) : null;
        } catch (Throwable) {
            return null;
        }
    }
}
