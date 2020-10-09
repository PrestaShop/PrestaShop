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

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class adds a function to twig template which points to back url if such is found in current request.
 */
class PathWithBackUrlExtension extends AbstractExtension
{
    /**
     * @var RoutingExtension
     */
    private $routingExtension;

    /**
     * @var BackUrlProvider
     */
    private $backUrlProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RoutingExtension $routingExtension
     * @param BackUrlProvider $backUrlProvider
     * @param RequestStack|null $requestStack
     */
    public function __construct(
        RoutingExtension $routingExtension,
        BackUrlProvider $backUrlProvider,
        $requestStack
    ) {
        $this->routingExtension = $routingExtension;
        $this->backUrlProvider = $backUrlProvider;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'pathWithBackUrl',
                [$this, 'getPathWithBackUrl']
            ),
        ];
    }

    /**
     * Gets original path or back url path.
     *
     * @param string $name - route name
     * @param array $parameters - route parameters
     * @param bool $relative
     *
     * @return string
     */
    public function getPathWithBackUrl($name, $parameters = [], $relative = false)
    {
        $fallbackPath = $this->routingExtension->getPath($name, $parameters, $relative);

        if (null === $this->requestStack) {
            return $fallbackPath;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $fallbackPath;
        }

        $backUrl = $this->backUrlProvider->getBackUrl($request);

        if (!$backUrl) {
            return $fallbackPath;
        }

        return $backUrl;
    }
}
