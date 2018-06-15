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

namespace PrestaShopBundle\Controller\Admin\Improve\Payment;

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentMethodsController is responsible for 'Improve > Payment > Payment Methods' page
 */
class PaymentMethodsController extends FrameworkBundleAdminController
{
    /**
     * Show payment method modules
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $shopContext = $this->get('prestashop.adapter.shop.context');
        $isSingleShopContext = $shopContext->isSingleShopContext();
        $paymentModules = [];

        if ($isSingleShopContext) {
            $paymentModules = $this->getPaymentModulesToDisplay($legacyController);
        }

        return $this->render('@PrestaShop/Admin/Improve/Payment/PaymentMethods/payment_methods.html.twig', [
            'paymentModules' => $paymentModules,
            'isSingleShopContext' => $isSingleShopContext,
            'layoutTitle' => $this->trans('Payment Methods', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ]);
    }

    /**
     * Gets installed and enabled payment modules for PaymentMethods controller to display
     *
     * @param string $legacyController
     *
     * @return ModuleInterface[]
     */
    protected function getPaymentModulesToDisplay($legacyController)
    {
        $tabModuleListProvider = $this->get('prestashop.adapter.module.data_provider.tab_module_list');
        $moduleDataProvider = $this->get('prestashop.adapter.data_provider.module');
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $adminModuleRepository = $this->get('prestashop.core.admin.module.repository');

        $tabModuleNames = $tabModuleListProvider->getTabModules($legacyController);

        $installedModules = $adminModuleRepository->getInstalledModules();
        $installedModuleNames = array_keys($installedModules);

        $paymentModulesToDisplay = [];
        foreach ($tabModuleNames as $moduleName) {
            if (!in_array($moduleName, $installedModuleNames) ||
                !$moduleDataProvider->can('configure', $moduleName)
            ) {
                continue;
            }

            $installedModule = $installedModules[$moduleName];
            if ($installedModule->database->get('active')) {
                $paymentModulesToDisplay[] = $modulePresenter->present($installedModule);
            }
        }

        return $paymentModulesToDisplay;
    }
}
