<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
