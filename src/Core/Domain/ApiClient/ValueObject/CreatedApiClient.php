<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;

class CreatedApiClient
{
    private ApiClientId $apiClientId;
    private string $secret;

    public function __construct(int $apiClientId, ?string $secret = null)
    {
        $this->apiClientId = new ApiClientId($apiClientId);
        if (empty($secret)) {
            throw new ApiClientConstraintException(sprintf('Invalid api client secret "%s".', var_export($secret, true)), ApiClientConstraintException::INVALID_SECRET);
        }
        $this->secret = $secret;
    }

    public function getApiClientId(): ApiClientId
    {
        return $this->apiClientId;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
