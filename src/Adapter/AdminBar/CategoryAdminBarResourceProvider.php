<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use CategoryController;
use Throwable;

final class CategoryAdminBarResourceProvider implements AdminBarResourceProviderInterface
{
    public function getResource(object $controller): ?AdminBarResource
    {
        if (!$controller instanceof CategoryController) {
            return null;
        }

        try {
            $id = (int) $controller->getCategory()->id;

            return $id > 0 ? new AdminBarResource('category', $id) : null;
        } catch (Throwable) {
            return null;
        }
    }
}
