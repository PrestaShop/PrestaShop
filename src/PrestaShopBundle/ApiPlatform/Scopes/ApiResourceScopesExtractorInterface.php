<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Scopes;

interface ApiResourceScopesExtractorInterface
{
    /**
     * Returns all installed resource scopes even the ones that are not enabled for now.
     *
     * @return ApiResourceScopes[]
     */
    public function getAllApiResourceScopes(): array;

    /**
     * Returns resource scopes for core and ENABLED modules.
     *
     * @return ApiResourceScopes[]
     */
    public function getEnabledApiResourceScopes(): array;
}
