<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleProviderInterface;
use PrestaShopBundle\Entity\Repository\ModuleRepository;

final class PaymentModuleProvider implements PaymentModuleProviderInterface
{
    /**
     * @var AddonRepositoryInterface
     */
    private $addonRepository;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param AddonRepositoryInterface $addonRepository
     * @param ModuleRepository $moduleRepository
     * @param int $shopId
     */
    public function __construct(
        AddonRepositoryInterface $addonRepository,
        ModuleRepository $moduleRepository,
        $shopId
    ) {
        $this->addonRepository = $addonRepository;
        $this->moduleRepository = $moduleRepository;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentModuleList()
    {
        $filters = (new AddonListFilter())
            ->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED);

        $modules = $this->addonRepository->getFilteredList($filters);
        $paymentModules = [];

        /** @var Module $module */
        foreach ($modules as $module) {
            if ($module->attributes->get('is_paymentModule')) {
                $restrictedModuleCountries = $this->moduleRepository->findCountryIdsByModuleAndShopId(
                    $module->database->get('id'),
                    $this->shopId
                );

                $restrictedModuleCurrencies = $this->moduleRepository->findCurrencyIdsByModuleAndShopId(
                    $module->database->get('id'),
                    $this->shopId
                );

                $restrictedModuleGroups = $this->moduleRepository->findGroupIdsByModuleAndShopId(
                    $module->database->get('id'),
                    $this->shopId
                );

                $restrictedModuleCarriers = $this->moduleRepository->findCarrierReferenceIdsByModuleAndShopId(
                    $module->database->get('id'),
                    $this->shopId
                );

                $module->attributes->set('countries', $restrictedModuleCountries);
                $module->attributes->set('currencies', $restrictedModuleCurrencies);
                $module->attributes->set('groups', $restrictedModuleGroups);
                $module->attributes->set('carriers', $restrictedModuleCarriers);

                $paymentModules[] = $module;
            }
        }

        return $paymentModules;
    }
}
