<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
