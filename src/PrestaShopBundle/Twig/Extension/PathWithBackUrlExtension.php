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

namespace PrestaShopBundle\Twig\Extension;

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
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @var RoutingExtension
     */
    private $routingExtension;

    /**
     * @param RoutingExtension $routingExtension
     * @param RequestStack|null $requestStack
     */
    public function __construct(
        RoutingExtension $routingExtension,
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
        $this->routingExtension = $routingExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'pathWithBackUrl',
                [$this, 'getPathWithBackUrl'],
                ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]
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

        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            return $fallbackPath;
        }

        $backUrl = $currentRequest->query->get('back-url');

        if (!$backUrl) {
            return $fallbackPath;
        }

        return urldecode($backUrl);
    }
}
