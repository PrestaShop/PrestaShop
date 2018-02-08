<?php
/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > Product Settings" page
 */
class ProductPreferencesController extends FrameworkBundleAdminController
{
    /**
     * Show product preferences form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if (!in_array($this->authorizationLevel($legacyController), [PageVoter::LEVEL_READ])) {
            //@todo: reidirect employee to default page
        }

        $form = $this->get('prestashop.admin.product_preferences.form_handler')->getForm();

        $twigValues = [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Product Settings', [], 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkAction' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
        ];

        return $this->render('@ShopParameters/product_preferences.html.twig', $twigValues);
    }

    /**
     * Process product preferences form
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if (!in_array(
            $this->authorizationLevel($legacyController),
            [
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            ]
        )) {
            $this->addFlash('error', $this->trans('You do not have permission to edit this', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_product_preferences');
        }

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
