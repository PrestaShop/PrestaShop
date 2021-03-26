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

namespace PrestaShopBundle\Controller\Admin\Improve\Payment;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentPreferencesController is responsible for "Improve > Payment > Preferences" page.
 */
class PaymentPreferencesController extends FrameworkBundleAdminController
{
    /**
     * Show payment preferences page.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *      message="Access denied."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $carrierRestrictionsForm = $this->getPaymentCarrierRestrictionsFormHandler()->getForm();
        $countryRestrictionsForm = $this->getPaymentCountryRestrictionsFormHandler()->getForm();
        $currencyRestrictionsForm = $this->getPaymentCurrencyRestrictionsFormHandler()->getForm();
        $groupRestrictionsForm = $this->getPaymentGroupRestrictionsFormHandler()->getForm();

        $legacyController = $request->attributes->get('_legacy_controller');

        $paymentModulesListProvider = $this->get('prestashop.adapter.module.payment_module_provider');
        $shopContext = $this->get('prestashop.adapter.shop.context');

        $isSingleShopContext = $shopContext->isSingleShopContext();

        $paymentModulesCount = 0;
        $carrierRestrictionsView = $countryRestrictionsView = $currencyRestrictionsView = $groupRestrictionsView = null;

        if ($isSingleShopContext) {
            $paymentModulesCount = count($paymentModulesListProvider->getPaymentModuleList());
            $carrierRestrictionsView = $carrierRestrictionsForm->createView();
            $countryRestrictionsView = $countryRestrictionsForm->createView();
            $currencyRestrictionsView = $currencyRestrictionsForm->createView();
            $groupRestrictionsView = $groupRestrictionsForm->createView();
        }

        return $this->render('@PrestaShop/Admin/Improve/Payment/Preferences/payment_preferences.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'paymentCurrencyRestrictionsForm' => $currencyRestrictionsView,
            'paymentCountryRestrictionsForm' => $countryRestrictionsView,
            'paymentGroupRestrictionsForm' => $groupRestrictionsView,
            'paymentCarrierRestrictionsForm' => $carrierRestrictionsView,
            'isSingleShopContext' => $isSingleShopContext,
            'paymentModulesCount' => $paymentModulesCount,
            'layoutTitle' => $this->trans('Preferences', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Process payment modules preferences carrier restrictions form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="Access denied.",
     *     redirectRoute="admin_payment_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPaymentCarrierRestrictionsFormAction(Request $request): RedirectResponse
    {
        $carrierRestrictionsFormHandler = $this->getPaymentCarrierRestrictionsFormHandler();

        return $this->processForm($carrierRestrictionsFormHandler, $request);
    }

    /**
     * Process payment modules preferences country restrictions form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="Access denied.",
     *     redirectRoute="admin_payment_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPaymentCountryRestrictionsFormAction(Request $request): RedirectResponse
    {
        $countryRestrictionsFormHandler = $this->getPaymentCountryRestrictionsFormHandler();

        return $this->processForm($countryRestrictionsFormHandler, $request);
    }

    /**
     * Process payment modules preferences currency restrictions form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="Access denied.",
     *     redirectRoute="admin_payment_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPaymentCurrencyRestrictionsFormAction(Request $request): RedirectResponse
    {
        $currencyRestrictionsFormHandler = $this->getPaymentCurrencyRestrictionsFormHandler();

        return $this->processForm($currencyRestrictionsFormHandler, $request);
    }

    /**
     * Process payment modules preferences group restrictions form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="Access denied.",
     *     redirectRoute="admin_payment_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPaymentGroupRestrictionsFormAction(Request $request): RedirectResponse
    {
        $groupRestrictionsFormHandler = $this->getPaymentGroupRestrictionsFormHandler();

        return $this->processForm($groupRestrictionsFormHandler, $request);
    }

    /**
     * Processes the form in a generic way.
     *
     * @param FormHandlerInterface $formHandler
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function processForm(FormHandlerInterface $formHandler, Request $request): RedirectResponse
    {
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $errors = $formHandler->save($data);
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_payment_preferences');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getPaymentCarrierRestrictionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.payment_carrier_restrictions.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getPaymentCountryRestrictionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.payment_country_restrictions.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getPaymentCurrencyRestrictionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.payment_currency_restrictions.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getPaymentGroupRestrictionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.payment_group_restrictions.form_handler');
    }
}
