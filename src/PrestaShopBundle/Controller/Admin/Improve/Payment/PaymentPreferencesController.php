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

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentPreferencesController extends FrameworkBundleAdminController
{
    /**
     * Show payment preferences page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $paymentPreferencesForm = $this->getPaymentPreferencesFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/Payment/Preferences/payment_preferences.html.twig', [
            'paymentPreferencesForm' => $paymentPreferencesForm->createView(),
        ]);
    }

    /**
     * Process payment modules preferences form
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $paymentPreferencesFormHandler = $this->getPaymentPreferencesFormHandler();

        $paymentPreferencesForm = $paymentPreferencesFormHandler->getForm();
        $paymentPreferencesForm->handleRequest($request);

        if ($paymentPreferencesForm->isSubmitted()) {
            $paymentPreferences = $paymentPreferencesForm->getData();

            $errors = $paymentPreferencesFormHandler->save($paymentPreferences);
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_payment_preferences');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_payment_preferences');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getPaymentPreferencesFormHandler()
    {
        return $this->get('prestashop.admin.payment_preferences.form_handler');
    }
}
