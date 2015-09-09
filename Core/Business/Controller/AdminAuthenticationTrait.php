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
namespace PrestaShop\PrestaShop\Core\Business\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Business\Routing\AdminRouter;

/**
 * This Trait will add middleware functions to check authentication state.
 */
trait AdminAuthenticationTrait
{
    /**
     * This trait will return false if the user is not authenticated. This will avoid calling controller action.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function initActionCheckAuthenticated(Request &$request, Response &$response)
    {
        $authManager = $this->container->make('Adapter_AuthenticationManager');
        return (!$this->isAuthenticationNeeded() || $authManager->isAdminAuthenticated());
    }

    /**
     * This trait will prepare redirection to the login page.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function closeActionCheckAuthenticated(Request &$request, Response &$response)
    {
        $authManager = $this->container->make('Adapter_AuthenticationManager');
        AdminRouter::getInstance()->setForbiddenRedirection($authManager->getAdminLoginUrl());
        return true;
    }
    
    /**
     * Defined in AdminController class. Allow trait methods to call it directly.
     */
    abstract protected function isAuthenticationNeeded();
}
