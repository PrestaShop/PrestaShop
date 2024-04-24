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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Form\Admin\LoginType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends PrestaShopAdminController
{
    public function __construct(
        private readonly ShopContext $shopContext
    ) {
    }

    /**
     * This route and controller are defined in the firewall as login_path and check_path
     * so the controller doesn't need to handle the form submission logic, it is handled
     * internally by the FormLoginAuthenticator
     *
     * See https://symfony.com/doc/current/security.html#form-login
     *
     * @param Security $security
     *
     * @return Response
     */
    public function loginAction(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->redirectToRoute('admin_homepage');
        }

        $loginForm = $this->createForm(LoginType::class);

        return $this->render('@PrestaShop/Admin/Login/login.html.twig', [
            'loginForm' => $loginForm,
            'imgDir' => $this->shopContext->getBaseURI() . 'img/',
            'shopName' => $this->getConfiguration()->get('PS_SHOP_NAME'),
        ]);
    }

    /**
     * This controller is not even called since the logout_path is defined in the firewall
     * so the logout path is watched and Symfony handles the logout part and the redirection
     * but we still need to define a route to benefit from the _legacy_link feature so it
     * doesn't hurt to have a consistent controller here anyway.
     *
     * See https://symfony.com/doc/current/security.html#logging-out
     *
     * @param Security $security
     *
     * @return RedirectResponse
     */
    public function logoutAction(Security $security): RedirectResponse
    {
        if ($security->getUser()) {
            $security->logout();
        }

        return $this->redirectToRoute('admin_login');
    }

    /**
     * Automatically redirects to the Employee configured homepage, or AdminDashboard
     * as a fallback, or to the login in case the employee is not logged in.
     *
     * @param Security $security
     * @param TabRepository $tabRepository
     * @param LegacyContext $legacyContext
     *
     * @return RedirectResponse
     */
    public function homepageAction(Security $security, TabRepository $tabRepository, LegacyContext $legacyContext): RedirectResponse
    {
        $loggedUser = $security->getUser();
        if ($loggedUser instanceof Employee) {
            $homeUrl = null;
            if (!empty($loggedUser->getDefaultTabId())) {
                /** @var Tab|null $defaultTab */
                $defaultTab = $tabRepository->findOneBy(['id' => $loggedUser->getDefaultTabId()]);
                if (!empty($defaultTab)) {
                    if (!empty($defaultTab->getRouteName())) {
                        $homeUrl = $this->generateUrl($defaultTab->getRouteName());
                    } elseif (!empty($defaultTab->getClassName())) {
                        $homeUrl = $legacyContext->getAdminLink($defaultTab->getClassName());
                    }
                }
            }

            if (null === $homeUrl) {
                $homeUrl = $legacyContext->getAdminLink('AdminDashboard');
            }

            return $this->redirect($homeUrl);
        }

        return $this->redirectToRoute('admin_login');
    }
}
