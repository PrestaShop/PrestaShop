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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $form = $this->get('prestashop.admin.product_preferences.form_handler')->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/product_preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Product Settings', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkAction' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Process product preferences form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.product_preferences.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $errors = $formHandler->save($data);

            if (0 === count($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_product_preferences');
    }
}
