<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use ProductController;
use Throwable;

final class ProductAdminBarResourceProvider implements AdminBarResourceProviderInterface
{
    public function getResource(object $controller): ?AdminBarResource
    {
        if (!$controller instanceof ProductController) {
            return null;
        }

        try {
            $id = (int) $controller->getProduct()?->id;

            return $id > 0 ? new AdminBarResource('product', $id) : null;
        } catch (Throwable) {
            return null;
        }
    }
}
