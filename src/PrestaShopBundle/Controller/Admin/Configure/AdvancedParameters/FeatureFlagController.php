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
        $featureFlagsFormHandler = $this->get('prestashop.admin.feature_flags.form_handler');
        $featureFlagsForm = $featureFlagsFormHandler->getForm();

        $featureFlagsForm->handleRequest($request);

        if ($featureFlagsForm->isSubmitted() && $featureFlagsForm->isValid()) {
            $errors = $featureFlagsFormHandler->save($featureFlagsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }

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
            'multistoreForcedContextInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                'Admin.Notifications.Info'
            ),
            'displayAllShopsContextForced' => ($this->get('prestashop.adapter.multistore_feature')->isUsed()
                && $this->get('prestashop.adapter.shop.context')->isShopContext()),
        ]);
    }
}
