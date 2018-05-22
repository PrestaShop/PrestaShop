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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller responsible of "Configure > Shop Parameters > Customer Settings" page
 */
class CustomerPreferencesController extends FrameworkBundleAdminController
{
    /**
     * Show customer preferences page
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/customer_preferences.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     * @return array Template parameters
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $form = $this->get('prestashop.admin.customer_preferences.form_handler')->getForm();

        return [
            'layoutTitle' => $this->trans('Customers', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'form' => $form->createView(),
        ];
    }

    /**
     * Process the Customer Preferences configuration form.
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to update this.", route="admin_customer_preferences")
     * @DemoRestricted(redirectRoute="admin_customer_preferences")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.customer_preferences.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            if ($errors = $formHandler->save($data)) {
                $this->flashErrors($errors);

                return $this->redirectToRoute('admin_customer_preferences');
            }

            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_customer_preferences');
    }
}
