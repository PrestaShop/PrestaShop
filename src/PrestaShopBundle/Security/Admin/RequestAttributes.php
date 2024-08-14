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

namespace PrestaShopBundle\Security\Admin;

/**
 * This class contains constants for specific attributes used on the request to add some features.
 */
class RequestAttributes
{
    /**
     * Setting this attribute to true on a request makes it "anonymous" or "public access", meaning
     * it can be accessed even without being authenticated and no CSRF token will be added in the
     * URL.
     *
     * It is equivalent to settings an access_control in the framework config except this attribute can
     * be set on a particular route settings which is very convenient for modules that can't modify the
     * access controls.
     *
     * Route example:
     *
     *  public_anonymous_route:
     *      path: /public_anonymous_route
     *      defaults:
     *          _controller: PrestaShop\Module\PublicRoute\AnonymousController::anonymousAction
     *          _anonymous_controller: true
     */
    public const ANONYMOUS_CONTROLLER_ATTRIBUTE = '_anonymous_controller';
}
