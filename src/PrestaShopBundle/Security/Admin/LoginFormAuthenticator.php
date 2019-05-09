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

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AuthenticateEmployeeCommand;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDefaultPageProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Responsible for authenticating the admin user using the login form.
 */
final class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var EmployeeDefaultPageProviderInterface
     */
    private $defaultPageProvider;

    /**
     * @param Hashing $hashing
     * @param RouterInterface $router
     * @param CommandBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param Tools $tools
     * @param EmployeeDefaultPageProviderInterface $defaultPageProvider
     */
    public function __construct(
        Hashing $hashing,
        RouterInterface $router,
        CommandBusInterface $commandBus,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        Tools $tools,
        EmployeeDefaultPageProviderInterface $defaultPageProvider
    ) {
        $this->hashing = $hashing;
        $this->router = $router;
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->tools = $tools;
        $this->defaultPageProvider = $defaultPageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        // only authenticate on login route
        return '_admin_login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('_admin_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $credentials = $request->request->get('login');

        return [
            'username' => $credentials['email'],
            'password' => $credentials['password'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials['username']) {
            return;
        }

        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->hashing->checkHash($credentials['password'], $user->getPassword());
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->router->generate('_admin_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $userId = $token->getUser()->getId();

        $authenticationCommand = new AuthenticateEmployeeCommand($userId);
        $this->commandBus->handle($authenticationCommand);

        $this->logger->info(
            $this->translator->trans(
                'Back office connection from %ip%',
                ['%ip%' => $this->tools->getIpAddress()],
                'Admin.Advparameters.Feature'
            ),
            [
                'allow_duplicate' => true,
            ]
        );

        return new RedirectResponse($this->defaultPageProvider->getDefaultPageUrl($userId));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return true;
    }
}
