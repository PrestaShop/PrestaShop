<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * This class allows to redirect to back url.
 */
final class BackUrlResponseListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $originalResponse = $event->getResponse();

        if (!$originalResponse instanceof RedirectResponse) {
            return;
        }

        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            return;
        }

        $backUrl = $currentRequest->query->get('back-url');

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
