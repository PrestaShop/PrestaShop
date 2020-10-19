<?php
/*
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

namespace PrestaShopBundle\EventListener;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tools;
use Validate;

class SessionListener
{
    const AUTH_COOKIE_LIFETIME = 3600;

    /**
     * @var LegacyContext
     */
    private $context;

    public function __construct(LegacyContext $context)
    {
        $this->context = $context;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (isset($_GET['logout'])) {
            $this->context->getContext()->employee->logout();
        }
        if (isset($this->context->getContext()->cookie->last_activity)) {
            if ($this->context->getContext()->cookie->last_activity + self::AUTH_COOKIE_LIFETIME < time()) {
                $this->context->getContext()->employee->logout();
            } else {
                $this->context->getContext()->cookie->last_activity = time();
            }
        }

        $legacyControllerName = Tools::getValue('controller');
        $controller = $this->context->getContext()->controller;

        if (
            !$controller->isAnonymousAllowed()
            && $legacyControllerName !== 'AdminLogin'
            && (
                !isset($this->context->getContext()->employee)
                || !$this->context->getContext()->employee->isLoggedBack()
            )
        ) {
            $this->context->getContext()->employee->logout();
            $redirectUrl = $this->context->getContext()->link->getAdminLink('AdminLogin');
            if (!isset($_GET['logout']) && $legacyControllerName !== 'AdminNotFound') {
                $redirectUrl .= '&redirect=' . $legacyControllerName;
            }
            if (Tools::getValue('email') && Validate::isEmail(Tools::getValue('email'))) {
                $redirectUrl .= '&email=' . Tools::getValue('email');
            }
            $event->setResponse(new RedirectResponse($redirectUrl));
        }

        Employee::setLastConnectionDate($this->context->getContext()->employee->id);
    }
}
