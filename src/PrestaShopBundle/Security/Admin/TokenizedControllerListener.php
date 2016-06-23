<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Security\Admin;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

/**
 * Class TokenizedControllerListener: A middleware to check CSRF token value in POST HTTP requests.
 *
 * The intent/tokenID is the sf route name of the action.
 * A POST request is checked only if the controller implements TokenizedController
 *
 * In case of error, a warning is triggered for now. No blocking behavior for ALPHA, but we must make it blocking after 1.7.0.
 * FIXME: >1.7.0: this should be blocking
 *
 * @package PrestaShopBundle\Security\Admin
 */
class TokenizedControllerListener
{
    /**
     * @var CsrfTokenManager
     */
    private $csrfProvider;

    public function __construct(CsrfTokenManager $csrfProvider)
    {
        $this->csrfProvider = $csrfProvider;
    }

    /**
     * Token check listener middleware method.
     *
     * This method is called by Sf Kernel during routing process, before controller/action call.
     * This is a middleware that will block/warns about wrong or missing CSRF token.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();

        /*
         * Check Route:
         * - avoid cases when an action is called from another action (no _route during the subcall)
         * - exception for debug tools
         */
        $routeName = $request->attributes->get('_route', false);
        if ($routeName === false || $routeName == '_wdt') {
            return;
        }

        // For now, if it's not a POST or if controller does not implement TokenizedController, do not check token
        if (!$request->isMethod('POST') || !($controller[0] instanceof TokenizedController)) {
            return;
        }

        // Compute server side token
        $tokenId = $request->attributes->get('_csrf_id', $routeName);
        $serverToken = $this->csrfProvider->getToken($tokenId)->getValue();

        // retrieve client token (sent trough POST request) and server-side token.
        $clientToken = $request->request->get('csrf', false);

        // TEST !
        if ($clientToken === false) {
            $request->getSession()->getFlashBag()->add('error', 'CSRF token missing');
            //throw new AccessDeniedHttpException('This action needs a token!');
            // TODO: remplacer le flashBag par l'exception pour rendre l'erreur bloquante.
            return;
        }
        if ($clientToken !== $serverToken) {
            $request->getSession()->getFlashBag()->add('error', 'CSRF tokens not equal: '.$clientToken.' != '.$serverToken);
            //throw new AccessDeniedHttpException('This action needs a valid token!');
            // TODO: remplacer le flashBag par l'exception pour rendre l'erreur bloquante.
        }
    }
}
