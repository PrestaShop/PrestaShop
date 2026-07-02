<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

/**
 * Enumerates the Admin API endpoints an extra property definition can be associated with
 * (the URI templates targeted by ExtraPropertyDefinition::getAssociatedApis()).
 */
interface ApiEndpointCatalogInterface
{
    /**
     * @return list<ApiEndpointEntry> sorted by URI template
     */
    public function getAll(): array;

    /**
     * The given path is normalized (single leading slash, no trailing slash) before comparison.
     */
    public function hasUriTemplate(string $path): bool;
}
