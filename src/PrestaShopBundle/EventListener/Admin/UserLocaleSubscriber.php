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

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Security\Admin\SessionEmployeeInterface;
use PrestaShopBundle\Security\Admin\SessionEmployeeProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    public const USER_LOCALE_PRIORITY = 15;

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly LanguageRepositoryInterface $langRepository,
        private readonly Security $security,
        private readonly SessionEmployeeProvider $sessionEmployeeProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority is set after Symfony LocaleListener, so it can override Symfony default locale
            // setting based on request attributes, but before LocaleAwareListener where the translator
            // locale is set, so we can define the chosen locale based on PrestaShop logic
            KernelEvents::REQUEST => [['onKernelRequest', self::USER_LOCALE_PRIORITY]],
        ];
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->security->getUser() instanceof SessionEmployeeInterface) {
            $employee = $this->security->getUser();
        } else {
            $employee = $this->sessionEmployeeProvider->getEmployeeFromSession($event->getRequest());
        }

        if ($employee instanceof SessionEmployeeInterface) {
            $locale = $employee->getDefaultLocale();
        } else {
            $locale = $this->langRepository->find($this->configuration->get('PS_LANG_DEFAULT'))->getLocale();
        }

        $request = $event->getRequest();
        $request->setDefaultLocale($locale);
        $request->setLocale($locale);
    }
}
