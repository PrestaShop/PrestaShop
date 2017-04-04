<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Security;

use Access;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Admin Middleware security
 */
class Admin
{
    private $context;
    private $legacyContext;
    private $securityTokenStorage;
    private $userProvider;

    /**
     * Constructor.
     *
     * @param LegacyContext $context
     * @param TokenStorage $securityTokenStorage
     */
    public function __construct(LegacyContext $context, TokenStorage $securityTokenStorage, UserProviderInterface $userProvider)
    {
        $this->context = $context;
        $this->legacyContext = $context->getContext();
        $this->securityTokenStorage = $securityTokenStorage;
        $this->userProvider = $userProvider;
    }

    /**
     * Check if employee is logged in
     * If not loggedin in, redirect to admin home page
     *
     * @param GetResponseEvent $event
     *
     * @return bool or redirect
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        //if employee loggdin in legacy context, authenticate him into sf2 security context
        if (isset($this->legacyContext->employee) && $this->legacyContext->employee->isLoggedBack()) {
            $user = $this->userProvider->loadUserByUsername($this->legacyContext->employee->email);
            $token = new UsernamePasswordToken($user, null, 'admin', $user->getRoles());
            $this->securityTokenStorage->setToken($token);

            return true;
        }

        //employee not logged in
        $event->stopPropagation();

        //if http request - add 403 error
        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()) {
            header("HTTP/1.1 403 Forbidden");
            exit();
        }

        //redirect to admin home page
        header("Location: ".$this->context->getAdminLink('', false));
        exit();
    }
}
