<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\Query;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\ApiClientId;

class GetApiClientForEditing
{
    private ApiClientId $apiClientId;

    public function __construct(int $apiClientId)
    {
        $this->apiClientId = new ApiClientId($apiClientId);
    }

    public function getApiClientId(): ApiClientId
    {
        return $this->apiClientId;
    }
}
