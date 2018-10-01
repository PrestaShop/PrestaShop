<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeController handles pages under "Configure > Advanced Parameters > Team > Employees".
 */
class EmployeeController extends FrameworkBundleAdminController
{
    /**
     * Show employees list & options page.
     *
     * @return Response
     */
    public function indexAction()
    {
        $employeeOptionsFormHandler = $this->get('prestashop.admin.employee_options.form_handler');
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();

        $multistoreContextChecker = $this->get('prestashop.adapter.shop.context');

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/index.html.twig', [
            'employeeOptionsForm' => $employeeOptionsForm->createView(),
            'isAllShopContext' => $multistoreContextChecker->isAllShopContext(),
        ]);
    }

    /**
     * Save employee options.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveOptionsAction(Request $request)
    {
        $employeeOptionsFormHandler = $this->get('prestashop.admin.employee_options.form_handler');
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();
        $employeeOptionsForm->handleRequest($request);

        if ($employeeOptionsForm->isSubmitted()) {
            $errors = $employeeOptionsFormHandler->save($employeeOptionsForm->getData());

            if (!empty($errors)) {
                $this->flashErrors($errors);

                return $this->redirectToRoute('admin_employees_index');
            }

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_employees_index');
    }
}
