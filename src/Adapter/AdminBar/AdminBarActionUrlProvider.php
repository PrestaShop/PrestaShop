<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use PrestaShop\PrestaShop\Adapter\Security\AdminPathProvider;

/**
 * Builds local Back Office URLs for Admin Bar actions.
 *
 * Explicit action endpoints are relative to the Admin Bar path and restricted to local
 * path segments, to prevent extensions from generating arbitrary or external URLs from
 * the Front Office.
 */
final class AdminBarActionUrlProvider
{
    private const ADMIN_BAR_PATH_PREFIX = '/_admin-bar';

    public function __construct(private readonly AdminPathProvider $adminPathProvider)
    {
    }

    public function getUrl(AdminBarAction $action): ?string
    {
        $endpoint = $action->getEndpoint();
        if ($endpoint === null) {
            return null;
        }

        if (preg_match('#\A(?:/[A-Za-z0-9][A-Za-z0-9_-]*)+\z#', $endpoint) !== 1) {
            return null;
        }

        $adminPath = $this->adminPathProvider->getPath();
        if ($adminPath === null) {
            return null;
        }

        return rtrim($adminPath, '/') . self::ADMIN_BAR_PATH_PREFIX . $endpoint;
    }
}
