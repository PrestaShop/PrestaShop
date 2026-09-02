<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\Command;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\ApiClientId;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\ApiClientSecret;

class ForceApiClientSecretCommand
{
    private ApiClientId $apiClientId;

    private ApiClientSecret $secret;

    public function __construct(
        int $apiClientId,
        string $secret
    ) {
        $this->apiClientId = new ApiClientId($apiClientId);
        $this->secret = new ApiClientSecret($secret);
    }

    public function getApiClientId(): ApiClientId
    {
        return $this->apiClientId;
    }

    public function getSecret(): ApiClientSecret
    {
        return $this->secret;
    }
}
