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

/** The "Advanced parameters > Security > Security headers" page: static response headers, gated behind the 'csp' feature flag. */
class SecurityHeadersController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminSecurityHeaders')")]
    public function indexAction(
        #[Autowire(service: 'prestashop.admin.security_headers.form_handler')]
        FormHandlerInterface $securityHeadersFormHandler,
    ): Response {
        $this->assertFeatureEnabled();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/SecurityHeaders/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Security headers', [], 'Admin.Navigation.Menu'),
                'help_link' => $this->generateSidebarLink('AdminSecurityHeaders'),
                'securityHeadersForm' => $securityHeadersFormHandler->getForm()->createView(),
            ]
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_security_headers_index')]
    #[AdminSecurity("is_granted('update', 'AdminSecurityHeaders')", redirectRoute: 'admin_security_headers_index')]
    public function saveAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.security_headers.form_handler')]
        FormHandlerInterface $securityHeadersFormHandler,
    ): RedirectResponse {
        $this->assertFeatureEnabled();

        $form = $securityHeadersFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $securityHeadersFormHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_security_headers_index');
    }

    private function assertFeatureEnabled(): void
    {
        if (!$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            throw $this->createNotFoundException('The security headers feature is not enabled.');
        }
    }
}
