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

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Entity\Repository\ModuleRepository as ModuleEntityRepository;

/**
 * Class PaymentModuleListProvider is responsible for providing payment module list.
 */
final class PaymentModuleListProvider implements PaymentModuleListProviderInterface
{
    /**
     * @var ModuleRepositoryInterface
     */
    private $moduleRepository;

    /**
     * @var ModuleEntityRepository
     */
    private $moduleEntityRepository;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param ModuleRepositoryInterface $moduleRepository
     * @param ModuleEntityRepository $moduleEntityRepository
     * @param int $shopId
     */
    public function __construct(
        ModuleRepositoryInterface $moduleRepository,
        ModuleEntityRepository $moduleEntityRepository,
        int $shopId
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->moduleEntityRepository = $moduleEntityRepository;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentModuleList()
    {
        $modules = $this->moduleRepository->getInstalledModules();
        $paymentModules = [];

        /** @var Module $module */
        foreach ($modules as $module) {
            if ($module->attributes->get('is_paymentModule')) {
                $restrictedCountries = $this->moduleEntityRepository->findRestrictedCountryIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedCurrencies = $this->moduleEntityRepository->findRestrictedCurrencyIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedGroups = $this->moduleEntityRepository->findRestrictedGroupIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedCarriers = $this->moduleEntityRepository->findRestrictedCarrierReferenceIds(
                    $module->database->get('id'),
                    $this->shopId
                );

                $module->attributes->set('countries', $restrictedCountries);
                $module->attributes->set('currencies', $restrictedCurrencies);
                $module->attributes->set('groups', $restrictedGroups);
                $module->attributes->set('carriers', $restrictedCarriers);

                $paymentModules[$module->attributes->get('name')] = $module;
            }
        }

        return $paymentModules;
    }
}
