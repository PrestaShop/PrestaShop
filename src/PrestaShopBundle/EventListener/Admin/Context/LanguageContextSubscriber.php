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
