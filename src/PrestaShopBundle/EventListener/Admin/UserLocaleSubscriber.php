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
    public const USER_LOCALE_PRIORITY = 16;

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
            // Must run before LocaleAwareListener, which is the listener that propagates the request
            // locale into the kernel.locale_aware services (the translator among them). That listener
            // sits at priority 15, so this one has to be strictly greater: at equal priority the order
            // would depend on the order services happen to be registered in the compiled container.
            // 16 ties with Symfony LocaleListener, which is harmless here - without a _locale request
            // attribute it does not touch the request locale.
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
