<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Displays and saves the "Configure > Advanced parameters > Security > Content Security Policy" page.
 *
 * This is a fully native page (no legacy heritage): permissions use the hard-coded tab name
 * 'AdminSecurityCsp', and the page is gated behind the 'csp' feature flag.
 */
class CspController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminSecurityCsp')")]
    public function indexAction(
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
    ): Response {
        $this->assertFeatureEnabled();

        $cspForm = $cspFormHandler->getForm();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Csp/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => [],
                'layoutTitle' => $this->trans('Content Security Policy', [], 'Admin.Navigation.Menu'),
                'help_link' => $this->generateSidebarLink('AdminSecurityCsp'),
                'cspForm' => $cspForm->createView(),
            ]
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('update', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function saveAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
    ): RedirectResponse {
        $this->assertFeatureEnabled();

        $form = $cspFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $cspFormHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    private function assertFeatureEnabled(): void
    {
        if (!$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            throw $this->createNotFoundException('The Content Security Policy feature is not enabled.');
        }
    }
}
