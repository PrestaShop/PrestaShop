<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listener dedicated to set up Language context for the Back-Office/Admin application.
 */
class LanguageContextSubscriber implements EventSubscriberInterface
{
    /**
     * Priority lower than EmployeeContextListener so that EmployeeContext is correctly initialized
     */
    public const KERNEL_REQUEST_PRIORITY = EmployeeContextSubscriber::KERNEL_REQUEST_PRIORITY - 1;

    /**
     * Priority higher than Symfony router listener (which is 32)
     */
    public const BEFORE_ROUTER_PRIORITY = 33;

    public function __construct(
        private readonly LanguageContextBuilder $languageContextBuilder,
        private readonly EmployeeContext $employeeContext,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['initDefaultLanguageContext', self::BEFORE_ROUTER_PRIORITY],
                ['initLanguageContext', self::KERNEL_REQUEST_PRIORITY],
            ],
        ];
    }

    public function initDefaultLanguageContext(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $this->languageContextBuilder->setDefaultLanguageId($defaultLanguageId);
        $this->languageContextBuilder->setLanguageId($defaultLanguageId);
    }

    public function initLanguageContext(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->employeeContext->getEmployee()) {
            // Use the employee language if available
            $this->languageContextBuilder->setLanguageId($this->employeeContext->getEmployee()->getLanguageId());
        }
    }
}
