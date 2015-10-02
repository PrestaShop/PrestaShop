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
namespace PrestaShop\PrestaShop\Core\Business\Controller\ExecutionSequenceService;

use PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceWrapper;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Business\Routing\RoutingService;

/**
 * This middleware add event listeners to check authentication state before action execution.
 *
 * Reserved to the Admin interface (checks merchant/employees authentication, not the final customer).
 * This is used by AdminController (to be extended in all the subclasses).
 * If the authentication failed, this is not its responsibility to display a login page:
 * a redirection is done to the login controller.
 */
final class AuthenticationMiddleware extends ExecutionSequenceServiceWrapper
{
    /**
     * @var \Adapter_AuthenticationManager
     */
    private $authenticationManager;

    /**
     * @var RoutingService
     */
    private $routingService;

    /**
     * Constructor
     *
     * @param \Adapter_AuthenticationManager $authenticationManager
     * @param RoutingService $routing
     */
    public function __construct(\Adapter_AuthenticationManager $authenticationManager, RoutingService $routing)
    {
        $this->authenticationManager = $authenticationManager;
        $this->routingService = $routing;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getInitListeners()
     */
    public function getInitListeners()
    {
        return array(64 => array($this, 'checkAuthentication'));
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getCloseListeners()
     */
    public function getCloseListeners()
    {
        return array(0 => array($this, 'checkRedirection'));
    }

    /**
     * Stops event propagation if the merchant/employee is not logged in.
     *
     * @param BaseEvent $event
     */
    public function checkAuthentication(BaseEvent $event)
    {
        if (!$this->authenticationManager->isAdminAuthenticated()) {
            $event->stopPropagation();
        }
    }

    /**
     * Set the forbidden redirection to the proper URL.
     * Always set the value, but will be really used only if the controller action's
     * sequence indicate a forbidden action.
     *
     * @param BaseEvent $event
     */
    public function checkRedirection(BaseEvent $event)
    {
        $this->routingService->setForbiddenRedirection($this->authenticationManager->getAdminLoginUrl());
    }
}
