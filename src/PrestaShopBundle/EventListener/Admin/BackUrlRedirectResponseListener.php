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

namespace PrestaShopBundle\EventListener\Admin;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

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

    public function onKernelResponse(ResponseEvent $event)
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
        if (
            // No back url nothing to change
            empty($backUrl)
            // Redirect to the current url(save and stay) nothing to change
            || $this->areUrlsEquals($currentRequest->getRequestUri(), $originalResponse->getTargetUrl(), $currentRequest)
            // If back url is the same as the target (query parameters are ignored) nothing to change
            || $this->areUrlsEquals($backUrl, $originalResponse->getTargetUrl(), $currentRequest)
        ) {
            return;
        }

        $backUrlResponse = $originalResponse->setTargetUrl($backUrl);
        $event->setResponse($backUrlResponse);
    }

    /**
     * Compares if request url is equal to response url - in such case the back url should not work since the action
     * is supposed to be kept on the same url . E.g "save and stay" button click.
     *
     * @return bool
     */
    private function areUrlsEquals(
        string $urlA,
        string $urlB,
        Request $request
    ) {
        $parsedUrlA = parse_url($urlA);
        $parsedUrlB = parse_url($urlB);

        // Some URls may be relative, so we fill the missing part based on current request
        $missingParts = [
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'port' => $request->getPort(),
            'user' => $request->getUser(),
            'pass' => $request->getPassword(),
            'path' => $request->getPathInfo(),
        ];
        foreach ($missingParts as $checkedPart => $missingValue) {
            if (empty($parsedUrlA[$checkedPart])) {
                $parsedUrlA[$checkedPart] = $missingValue;
            }
            if (empty($parsedUrlB[$checkedPart])) {
                $parsedUrlB[$checkedPart] = $missingValue;
            }
        }

        // We don't check all the parts of the url, we omit the query and fragment part so if query parameters are different
        // the URLs are considered equal, this mostly allows appending some query parameter in redirection like &conf=4 used
        // by legacy pages to display alert messages
        foreach (['scheme', 'host', 'port', 'user', 'pass', 'path'] as $checkedPart) {
            if (($parsedUrlA[$checkedPart] ?? '') !== ($parsedUrlB[$checkedPart] ?? '')) {
                return false;
            }
        }

        // Now compare parameters, additional parameters are ignored but different values for same parameter means the url is not equal
        if (isset($parsedUrlA['query'], $parsedUrlB['query'])) {
            $parametersA = [];
            parse_str($parsedUrlA['query'], $parametersA);
            $parametersB = [];
            parse_str($parsedUrlB['query'], $parametersB);
            unset($parametersA['token'], $parametersB['token']);
            $allParameters = array_unique(array_merge(array_keys($parametersA), array_keys($parametersB)));
            $ignoredParameters = ['conf'];
            foreach ($allParameters as $parameterName) {
                // Some parameters can be modified and it's ok
                if (in_array($parameterName, $ignoredParameters)) {
                    continue;
                }
                if (!isset($parametersA[$parameterName]) || !isset($parametersB[$parameterName])) {
                    continue;
                }
                if ($parametersA[$parameterName] !== $parametersB[$parameterName]) {
                    return false;
                }
            }
        }

        return true;
    }
}
