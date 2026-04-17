<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

class ApiClientContext
{
    public function __construct(private ?ApiClient $apiClient)
    {
    }

    public function getApiClient(): ?ApiClient
    {
        return $this->apiClient;
    }
}
