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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag\FeatureFlagsType;

/**
 * Manages the "Configure > Advanced Parameters > Experimental Features" page.
 */
class FeatureFlagController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $multistoreFeature = $this->get('prestashop.adapter.multistore_feature');
        $featureFlagsModifier = $this->get('prestashop.core.feature_flags.modifier');
        $featureFlagsForm = $this->createForm(
            FeatureFlagsType::class,
            null,
            [
                'feature_flags' => $featureFlagsModifier->getAllFeatureFlags(),
                'is_multistore_active' => $multistoreFeature->isActive(),
            ]
        );

        $betaFeatureFlagsModifier = $this->get('prestashop.core.feature_flags_beta.modifier');
        $betaFeatureFlagsForm = $this->createForm(
            FeatureFlagsType::class,
            null,
            [
                'feature_flags' => $betaFeatureFlagsModifier->getAllFeatureFlags(),
                'is_multistore_active' => $multistoreFeature->isActive(),
            ]
        );

        $featureFlagsForm->handleRequest($request);
        $betaFeatureFlagsForm->handleRequest($request);

        if ($featureFlagsForm->isSubmitted() && $featureFlagsForm->isValid()) {
            $this->formSubmission($featureFlagsForm, $featureFlagsModifier);

            return $this->redirectToRoute('admin_feature_flags_index');
        }

        if ($betaFeatureFlagsForm->isSubmitted() && $betaFeatureFlagsForm->isValid()) {
            $this->formSubmission($betaFeatureFlagsForm, $betaFeatureFlagsModifier);

            return $this->redirectToRoute('admin_feature_flags_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/FeatureFlag/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Experimental Features', 'Admin.Advparameters.Feature'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'featureFlagsForm' => $featureFlagsForm->createView(),
            'betaFeatureFlagsForm' => $betaFeatureFlagsForm->createView(),
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => ($this->get('prestashop.adapter.multistore_feature')->isUsed()
                && $this->get('prestashop.adapter.shop.context')->isShopContext()),
        ]);
    }

    private function formSubmission($form, $modifier)
    {
        $errors = [];

        try {
            $modifier->updateConfiguration($form->getData());
        } catch(\Exception $e) {
            $error[] = $e->getMessage();
        }

        if (empty($errors)) {
            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        } else {
            $this->flashErrors($errors);
        }
    }
}
