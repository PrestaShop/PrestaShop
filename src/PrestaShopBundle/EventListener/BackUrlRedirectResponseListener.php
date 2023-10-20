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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\EventListener;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * This class allows to redirect to back url.
 */
final class BackUrlRedirectResponseListener
{
    /**
     * @var BackUrlProvider
     */
    private $backUrlProvider;

    /**
     * @var int|null
     */
    private $employeeId;

    /**
     * @param BackUrlProvider $backUrlProvider
     */
    public function __construct(
        BackUrlProvider $backUrlProvider,
        LegacyContext $legacyContext
   ) {
        $this->backUrlProvider = $backUrlProvider;
        $context = $legacyContext->getContext();
        if (null !== $context && $context->employee instanceof Employee) {
            $this->employeeId = $context->employee->id;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // No need to continue because the employee is not connected
        if (!$this->employeeId) {
            return;
        }

        $currentRequest = $event->getRequest();
        $originalResponse = $event->getResponse();

        if (!$originalResponse instanceof RedirectResponse) {
            return;
        }

        $backUrl = $this->backUrlProvider->getBackUrl($currentRequest);

        if ($backUrl && !$this->isRequestUrlEqualToResponseUrl($currentRequest, $originalResponse)) {
            $backUrlResponse = $originalResponse->setTargetUrl($backUrl);
            $event->setResponse($backUrlResponse);
        }
    }

    /**
     * Compares if request url is equal to response url - in such case the back url should not work since the action
     * is suppose to be kept on the same url . E.g "save and stay" button click.
     *
     * @param Request $currentRequest
     * @param RedirectResponse $originalResponse
     *
     * @return bool
     */
    private function isRequestUrlEqualToResponseUrl(
        Request $currentRequest,
        RedirectResponse $originalResponse
    ) {
        return $currentRequest->getRequestUri() === $originalResponse->getTargetUrl();
    }
}
