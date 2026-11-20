<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Payment;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentPreferencesController is responsible for "Improve > Payment > Preferences" page.
 */
class PaymentPreferencesController extends PrestaShopAdminController
{
    /**
     * Show payment preferences page.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.payment_preferences.form_handler')]
        FormHandlerInterface $paymentPreferencesFormHandler,
        #[Autowire(service: 'prestashop.adapter.module.payment_module_provider')]
        PaymentModuleListProviderInterface $paymentModulesListProvider
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $isSingleShopContext = $this->getShopContext()->getShopConstraint()->isSingleShopContext();

        $paymentPreferencesForm = null;
        $paymentModulesCount = 0;

        if ($isSingleShopContext) {
            $paymentModulesCount = count($paymentModulesListProvider->getPaymentModuleList());
            $paymentPreferencesForm = $paymentPreferencesFormHandler->getForm()->createView();
        }

        return $this->render('@PrestaShop/Admin/Improve/Payment/Preferences/payment_preferences.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'paymentPreferencesForm' => $paymentPreferencesForm,
            'isSingleShopContext' => $isSingleShopContext,
            'paymentModulesCount' => $paymentModulesCount,
            'layoutTitle' => $this->trans('Preferences', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Process payment modules preferences form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_payment_preferences')]
    public function processFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.payment_preferences.form_handler')]
        FormHandlerInterface $paymentPreferencesFormHandler
    ): RedirectResponse {
        $paymentPreferencesForm = $paymentPreferencesFormHandler->getForm();
        $paymentPreferencesForm->handleRequest($request);

        if ($paymentPreferencesForm->isSubmitted()) {
            if ($this->isSubmissionTruncated($request, $paymentPreferencesForm)) {
                $this->addFlash('error', $this->trans(
                    'Nothing has been saved: the form has more fields than your server accepts in one request (max_input_vars = %limit%), so part of it was lost. Increase max_input_vars in your PHP configuration and try again.',
                    ['%limit%' => ini_get('max_input_vars')],
                    'Admin.Notifications.Error'
                ));

                return $this->redirectToRoute('admin_payment_preferences');
            }

            $paymentPreferences = $paymentPreferencesForm->getData();

            $errors = $paymentPreferencesFormHandler->save($paymentPreferences);
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_payment_preferences');
            }

            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('admin_payment_preferences');
    }

    /**
     * Every checked box is a separate input variable, so with many payment modules and countries
     * the request can exceed PHP's max_input_vars, and PHP then silently drops the end of it.
     * The CSRF token is rendered after all the restrictions, so it is missing exactly in that case,
     * and saving would replace the restrictions with an incomplete set.
     */
    private function isSubmissionTruncated(Request $request, FormInterface $form): bool
    {
        $config = $form->getConfig();
        if (!$config->getOption('csrf_protection', false)) {
            return false;
        }

        $submittedData = $request->request->all($form->getName());

        return !array_key_exists($config->getOption('csrf_field_name', '_token'), $submittedData);
    }
}
