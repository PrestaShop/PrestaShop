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

namespace Tests\Integration\Utility;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Admin Middleware security
 */
class AdapterSecurityAdmin
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly TokenStorageInterface $securityTokenStorage,
        private readonly UserProviderInterface $userProvider
    ) {
    }

    /**
     * Aims to authenticate the employee present in the context, only on routes not concerning the API
     *
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $actualFirewall = $event->getRequest()->attributes->get('_firewall_context');

        if (null !== $actualFirewall && (str_ends_with($actualFirewall, 'api_token') || str_ends_with($actualFirewall, 'api'))) {
            return;
        }
        $employee = $this->context->getContext()->employee;

        if (null !== $employee && null !== $employee->email) {
            $user = $this->userProvider->loadUserByIdentifier($employee->email);
            $token = new UsernamePasswordToken($user, 'admin', $user->getRoles());
            $this->securityTokenStorage->setToken($token);
        }
    }
}
