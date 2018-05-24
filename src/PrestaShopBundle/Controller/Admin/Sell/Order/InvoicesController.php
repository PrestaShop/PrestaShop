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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller responsible of "Sell > Orders > Invoices" page
 */
class InvoicesController extends FrameworkBundleAdminController
{
    /**
     * Show order preferences page
     *
     * @param Request $request
     *
     * @Template("@PrestaShop/Admin/Sell/Orders/Invoices/invoices.html.twig")
     *
     * @return array Template parameters
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $form = $this->get('prestashop.admin.orders_invoices.form_handler')->getForm();

        return [
            'layoutTitle' => $this->trans('Invoices', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'form' => $form->createView(),
        ];
    }

    /**
     * Handle order settings form submit
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
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            ]
        )) {
            $this->addFlash(
                'error',
                $this->trans('You do not have permission to edit this', 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_order_invoices');
        }

        $formHandler = $this->get('prestashop.admin.orders_invoices.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            }
        }

        return $this->redirectToRoute('admin_order_invoices');
    }
}
