<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings" page.
 */
class CustomerPreferencesController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.customer_preferences.form_handler')]
        FormHandlerInterface $customerFormHandler,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $form = $customerFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/customer_preferences.html.twig', [
            'layoutTitle' => $this->trans('Customer settings', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generalForm' => $form->createView(),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_preferences')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_customer_preferences')]
    public function processAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.customer_preferences.form_handler')]
        FormHandlerInterface $customerFormHandler,
    ): Response {
        $form = $customerFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($errors = $customerFormHandler->save($data)) {
                $this->addFlashErrors($errors);

                return $this->redirectToRoute('admin_customer_preferences');
            }

            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_customer_preferences');
        }

        $legacyController = $request->attributes->get('_legacy_controller');

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/customer_preferences.html.twig', [
            'layoutTitle' => $this->trans('Customers', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generalForm' => $form->createView(),
        ]);
    }
}
