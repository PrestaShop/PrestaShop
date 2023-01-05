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

namespace PrestaShopBundle\EventListener;

use Context;
use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class UserLocaleListener
{
    /** @var Context|null */
    private $prestaShopContext;

    /** @var ShopConfigurationInterface */
    private $configuration;

    /**
     * @param LegacyContext $context
     * @param ShopConfigurationInterface $configuration
     */
    public function __construct(LegacyContext $context, ShopConfigurationInterface $configuration)
    {
        $this->prestaShopContext = $context->getContext();
        $this->configuration = $configuration;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (isset($this->prestaShopContext->employee) && $this->prestaShopContext->employee->isLoggedBack()) {
            $request = $event->getRequest();
            $locale = $this->getLocaleFromEmployee();
            $request->setDefaultLocale($locale);

            $request->setLocale($locale);
        }
    }

    /**
     * @return string
     */
    private function getLocaleFromEmployee(): string
    {
        $employee = $this->prestaShopContext->employee;
        $employeeLanguage = new Language($employee->id_lang);

        if (!$employeeLanguage->locale) {
            $employeeLanguage = new Language($this->configuration->get('PS_LANG_DEFAULT'));
        }

        return $employeeLanguage->locale;
    }
}
