<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Security;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Admin Middleware security.
 */
class Admin
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var TokenStorage
     */
    private $securityTokenStorage;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param LegacyContext $context
     * @param TokenStorage $securityTokenStorage
     * @param UserProviderInterface $userProvider
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        LegacyContext $context,
        TokenStorage $securityTokenStorage,
        UserProviderInterface $userProvider,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->context = $context;
        $this->securityTokenStorage = $securityTokenStorage;
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * If employee is logged in - set security token to the token storage.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $contextEmployee = $this->context->getContext()->employee;

        //if employee loggdin in legacy context, authenticate him into sf2 security context
        if (isset($contextEmployee) && $contextEmployee->isLoggedBack()) {
            $user = $this->userProvider->loadUserByUsername($contextEmployee->email);
            $token = new UsernamePasswordToken($user, null, 'admin', $user->getRoles());
            $this->securityTokenStorage->setToken($token);
        } elseif ($event->isMasterRequest()) {
            // If employee is not logged in - redirect to login page.
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('_admin_login')));
        }
    }
}
