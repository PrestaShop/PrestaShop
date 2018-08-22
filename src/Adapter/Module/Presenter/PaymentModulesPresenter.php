<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter\Module\Presenter;

use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\TabModuleListProviderInterface;

/**
 * Class PaymentModulesPresenter is responsible for presenting payment modules.
 */
class PaymentModulesPresenter
{
    /**
     * @var string It will use legacy controller name to get payment modules for controller
     */
    const PAYMENT_METHODS_CONTROLLER = 'AdminPayment';

    /**
     * @var TabModuleListProviderInterface
     */
    private $tabModuleListProvider;

    /**
     * @var ModuleDataProvider
     */
    private $moduleDataProvider;

    /**
     * @var PresenterInterface
     */
    private $modulePresenter;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @param TabModuleListProviderInterface $tabModuleListProvider
     * @param ModuleDataProvider $moduleDataProvider
     * @param PresenterInterface $modulePresenter
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(
        TabModuleListProviderInterface $tabModuleListProvider,
        ModuleDataProvider $moduleDataProvider,
        PresenterInterface $modulePresenter,
        ModuleRepository $moduleRepository
    ) {
        $this->tabModuleListProvider = $tabModuleListProvider;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->modulePresenter = $modulePresenter;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Get presented payment modules.
     *
     * @return array
     */
    public function present()
    {
        $tabModuleNames = $this->tabModuleListProvider->getTabModules(self::PAYMENT_METHODS_CONTROLLER);

        $installedModules = $this->moduleRepository->getInstalledModules();
        $installedModuleNames = array_keys($installedModules);

        $paymentModulesToDisplay = [];
        foreach ($tabModuleNames as $moduleName) {
            if (!in_array($moduleName, $installedModuleNames) ||
                !$this->moduleDataProvider->can('configure', $moduleName)
            ) {
                continue;
            }

            $installedModule = $installedModules[$moduleName];
            if ($installedModule->database->get('active')) {
                $paymentModulesToDisplay[] = $this->modulePresenter->present($installedModule);
            }
        }

        return $paymentModulesToDisplay;
    }
}
