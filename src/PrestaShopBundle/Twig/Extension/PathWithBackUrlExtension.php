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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\TwigFunction;

class PathWithBackUrlExtension extends RoutingExtension
{
    /**
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @param UrlGeneratorInterface $generator
     * @param RequestStack|null $requestStack
     */
    public function __construct(
        UrlGeneratorInterface $generator,
        RequestStack $requestStack
    ) {
        parent::__construct($generator);
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        $parentFunctions = parent::getFunctions();

        return array_merge(
            $parentFunctions,
            [
                new TwigFunction(
                    'pathWithBackUrl',
                    [$this, 'getPathWithBackUrl'],
                    ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]
                ),
            ]
        );
    }

    public function getPathWithBackUrl($name, $parameters = [], $relative = false)
    {
        $fallbackPath = $this->getPath($name, $parameters, $relative);
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
