<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Context;

use PrestaShop\PrestaShop\Core\Context\ApiClient;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;

/**
 * This decorator is used for test environment only it allows it makes the context mutable and allows
 * to vary its value in test scenarios. Not to use in prod code as the contexts are meant to be immutable.
 */
class ApiClientContextDecorator extends ApiClientContext
{
    private ApiClientContext $decoratedApiClientContext;

    private ?ApiClient $overriddenApiClient = null;

    private bool $useOverriddenValue = false;

    public function __construct(ApiClientContext $decoratedApiClientContext)
    {
        $this->decoratedApiClientContext = $decoratedApiClientContext;
        parent::__construct($decoratedApiClientContext->getApiClient());
    }

    public function getApiClient(): ?ApiClient
    {
        if ($this->useOverriddenValue) {
            return $this->overriddenApiClient;
        }

        return $this->decoratedApiClientContext->getApiClient();
    }

    /**
     * Once the value has been overridden it will we used instead of the initial one (even if it's null),
     * to disable this permanent override you can use resetOverriddenEmployee
     *
     * @param ApiClient|null $overriddenApiClient
     */
    public function setOverriddenApiClient(?ApiClient $overriddenApiClient): void
    {
        $this->useOverriddenValue = true;
        $this->overriddenApiClient = $overriddenApiClient;
    }

    /**
     * This method resets the override values, thus the decorator keeps acting as a simple proxy without impacting
     * the decorated service.
     */
    public function resetOverriddenApiClient(): void
    {
        $this->useOverriddenValue = false;
        $this->overriddenApiClient = null;
    }
}
