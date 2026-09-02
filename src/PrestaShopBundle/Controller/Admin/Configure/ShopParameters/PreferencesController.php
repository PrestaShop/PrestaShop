<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Shop Parameters > General" page.
 */
class PreferencesController extends PrestaShopAdminController
{
    public const CONTROLLER_NAME = 'AdminPreferences';

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.preferences.form_handler')]
        FormHandlerInterface $preferencesFormHandler,
    ): Response {
        $form = $preferencesFormHandler->getForm();

        return $this->doRenderForm($request, $form);
    }

    #[DemoRestricted(redirectRoute: 'admin_preferences')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_preferences')]
    public function processFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.preferences.form_handler')]
        FormHandlerInterface $preferencesFormHandler,
    ): Response {
        $this->dispatchHookWithParameters('actionAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $preferencesFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $preferencesFormHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->dispatchCommand(
                    new UpdateTabStatusByClassNameCommand(
                        'AdminShopGroup',
                        $this->getConfiguration()->get('PS_MULTISHOP_FEATURE_ACTIVE')
                    )
                );

                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_preferences');
            }

            $this->addFlashErrors($saveErrors);
        }

        return $this->doRenderForm($request, $form);
    }

    private function doRenderForm(Request $request, FormInterface $form): Response
    {
        // SSL URI is used for the merchant to check if he has SSL enabled
        $sslUri = 'https://' . $this->getShopContext()->getDomainSSL() . $request->getRequestUri();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Preferences', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $form->createView(),
            'isSslEnabled' => $this->getConfiguration()->get('PS_SSL_ENABLED'),
            'sslUri' => $sslUri,
        ]);
    }
}
