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

namespace PrestaShop\PrestaShop\Core\Security\OAuth2;

use ApiPlatform\Action\PlaceholderAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * Additional checker for api firewall, the pattern /api is too wide and matches
 * with existing legacy APIs so we add a more detailed condition that the URI pattern
 * matches AND it's an API handled by ApiPlatform.
 *
 * The legacy APIs should be moved/modified to use another prefix, then this matcher
 * can be removed as it will be overkill.
 */
class ApiRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        return in_array(
            $request->attributes->get('_controller'),
            [PlaceholderAction::class, 'api_platform.action.placeholder']
        );
    }
}
