<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
