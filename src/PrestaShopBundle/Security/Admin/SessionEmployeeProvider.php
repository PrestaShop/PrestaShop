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

use ErrorException;
use PrestaShopBundle\Entity\Employee\Employee;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This service is able to get the logged in employee info from the Symfony sessions,
 * it is exactly doing the same thing as the internal ContextListener but "manually"
 *
 * This is useful for listeners that are executed before the ContextListener, so they
 * can init some contexts based on employee data for example.
 *
 * This should not be used in any other context, when you need to get the logged user you
 * should rely on the Symfony\Bundle\SecurityBundle\Security service instead.
 *
 * @internal
 */
class SessionEmployeeProvider
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EmployeeProvider $employeeProvider,
        private readonly LoggerInterface $logger,
        private readonly string $sessionKey = '_security_main',
    ) {
    }

    /**
     * Most of this code is inspired from the ContextListener, it's just that we need to get the employee
     * before the firewall listener in order to preset the PrestaShop contexts.
     */
    public function getEmployeeFromSession(?Request $request = null): ?Employee
    {
        $userIdentifier = $this->getEmployeeIdentifierFromSession($request);
        if (!empty($userIdentifier)) {
            $employee = $this->employeeProvider->loadUserByIdentifier($userIdentifier);
            if ($employee instanceof Employee) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Sometimes only the employee identifier is needed (like for token generation)
     */
    public function getEmployeeIdentifierFromSession(?Request $request = null): ?string
    {
        $request = $request ?? $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }
        $session = $request->hasPreviousSession() ? $request->getSession() : null;
        if (null !== $session) {
            $token = $session->get($this->sessionKey);
            if (null !== $token) {
                $token = $this->safelyUnserialize($token);
                if ($token instanceof TokenInterface) {
                    return $token->getUser()->getUserIdentifier();
                }
            }
        }

        return null;
    }

    private function safelyUnserialize(string $serializedToken): mixed
    {
        $token = null;
        $prevUnserializeHandler = ini_set('unserialize_callback_func', __CLASS__ . '::handleUnserializeCallback');
        $prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler) {
            if (__FILE__ === $file) {
                throw new ErrorException($msg, 0x37313BC, $type, $file, $line);
            }

            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
        });

        try {
            $token = unserialize($serializedToken);
        } catch (ErrorException $e) {
            if (0x37313BC !== $e->getCode()) {
                throw $e;
            }
            $this->logger->warning('Failed to unserialize the security token from the session.', ['key' => $this->sessionKey, 'received' => $serializedToken, 'exception' => $e]);
        } finally {
            restore_error_handler();
            ini_set('unserialize_callback_func', $prevUnserializeHandler);
        }

        return $token;
    }

    /**
     * @internal
     */
    public static function handleUnserializeCallback(string $class): never
    {
        throw new ErrorException('Class not found: ' . $class, 0x37313BC);
    }
}
