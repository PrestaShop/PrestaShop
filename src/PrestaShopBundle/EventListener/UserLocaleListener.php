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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\EventListener;

use Context;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UserLocaleListener
{
    /** @var Context|null */
    private $prestaShopContext;

    /** @var ShopConfigurationInterface */
    private $configuration;

    /** @var LanguageRepositoryInterface */
    private $langRepository;

    /**
     * @param LegacyContext $context
     * @param ShopConfigurationInterface $configuration
     * @param LanguageRepositoryInterface $langRepository
     */
    public function __construct(LegacyContext $context, ShopConfigurationInterface $configuration, LanguageRepositoryInterface $langRepository)
    {
        $this->prestaShopContext = $context->getContext();
        $this->configuration = $configuration;
        $this->langRepository = $langRepository;
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (isset($this->prestaShopContext->employee) && $this->prestaShopContext->employee->isLoggedBack()) {
            $request = $event->getRequest();
            $locale = $this->getLocaleFromEmployee($this->prestaShopContext->employee);
            $request->setDefaultLocale($locale);

            $request->setLocale($locale);
        }
    }

    /**
     * @param Employee $employee
     *
     * @return string
     */
    private function getLocaleFromEmployee(Employee $employee): string
    {
        $employeeLanguage = $this->langRepository->find($employee->id_lang);

        if (!$employeeLanguage) {
            $employeeLanguage = $this->langRepository->find($this->configuration->get('PS_LANG_DEFAULT'));
        }

        return $employeeLanguage->getLocale();
    }
}
