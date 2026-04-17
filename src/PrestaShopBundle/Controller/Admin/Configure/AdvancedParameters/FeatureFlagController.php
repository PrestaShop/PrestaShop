<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Experimental Features" page.
 */
#[AllShopContext]
class FeatureFlagController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.feature_flags.stable_form_handler')]
        FormHandlerInterface $stableFormHandler,
        #[Autowire(service: 'prestashop.admin.feature_flags.beta_form_handler')]
        FormHandlerInterface $betaFormHandler,
    ): Response {
        $stableFeatureFlagsForm = $stableFormHandler->getForm();

        $stableFeatureFlagsForm->handleRequest($request);

        if ($stableFeatureFlagsForm->isSubmitted() && $stableFeatureFlagsForm->isValid()) {
            try {
                $errors = $stableFormHandler->save($stableFeatureFlagsForm->getData());
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }

            return $this->redirectToRoute('admin_feature_flags_index');
        }

        $betaFeatureFlagsForm = $betaFormHandler->getForm();

        $betaFeatureFlagsForm->handleRequest($request);

        if ($betaFeatureFlagsForm->isSubmitted() && $betaFeatureFlagsForm->isValid()) {
            try {
                $errors = $betaFormHandler->save($betaFeatureFlagsForm->getData());
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }

            return $this->redirectToRoute('admin_feature_flags_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/FeatureFlag/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('New & Experimental Features', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'stableFeatureFlagsForm' => $this->isFormEmpty($stableFeatureFlagsForm)
                ? null
                : $stableFeatureFlagsForm->createView(),
            'betaFeatureFlagsForm' => $this->isFormEmpty($betaFeatureFlagsForm)
                ? null
                : $betaFeatureFlagsForm->createView(),
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed() && $this->getShopContext()->getShopConstraint()->getShopId() !== null,
        ]);
    }

    private function isFormEmpty(FormInterface $form): bool
    {
        return $form->get('feature_flags')->count() === 0;
    }
}
