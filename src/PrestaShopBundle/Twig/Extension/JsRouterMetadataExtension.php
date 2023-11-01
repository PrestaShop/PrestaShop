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

use PrestaShopBundle\Service\DataProvider\UserProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides data needed for Javascript router component
 */
class JsRouterMetadataExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly UserProvider $userProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('js_router_metadata', [$this, 'getJsRouterMetadata']),
        ];
    }

    /**
     * Get base url and security token used for javascript router component.
     *
     * @return array
     */
    public function getJsRouterMetadata(): array
    {
        return [
            // base url for javascript router
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            //security token for javascript router
            'token' => $this->tokenManager->getToken($this->userProvider->getUsername())->getValue(),
        ];
    }
}
