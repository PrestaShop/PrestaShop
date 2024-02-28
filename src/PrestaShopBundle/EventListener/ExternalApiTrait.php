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

namespace PrestaShopBundle\EventListener;

use PrestaShopBundle\Controller\Api\OAuth2\AccessTokenController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Utility Trait, enabling the detection of whether a request is an external API request.
 * This allows us to condition listeners and thus control the creation of contexts.
 */
trait ExternalApiTrait
{
    /**
     * Return true if the request is accessing a resource API or the access token endpoint.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isExternalApiRequest(Request $request): bool
    {
        return str_starts_with($request->getBaseUrl(), '/oauth-api');
    }

    /**
     * Return true only for request that are Resource APIs, the controller should always be the same default
     * ApiPlatform empty controller api_platform.action.placeholder.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isResourceApiRequest(Request $request): bool
    {
        return str_starts_with($request->getBaseUrl(), '/oauth-api') && $request->attributes->get('_controller') !== AccessTokenController::class;
    }
}
