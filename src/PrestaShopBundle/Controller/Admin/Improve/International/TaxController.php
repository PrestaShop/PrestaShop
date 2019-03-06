<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TaxController is responsible for handling "Improve > International > Taxes" page.
 */
class TaxController extends FrameworkBundleAdminController
{
    /**
     * Show taxes page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $taxOptionsForm = $this->getTaxOptionsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Tax/index.html.twig', [
            'taxOptionsForm' => $taxOptionsForm->createView(),
        ]);
    }

    /**
     * Process tax options configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index"
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveOptionsAction(Request $request)
    {
        $taxOptionsFormHandler = $this->getTaxOptionsFormHandler();

        $taxOptionsForm = $taxOptionsFormHandler->getForm();
        $taxOptionsForm->handleRequest($request);

        if ($taxOptionsForm->isSubmitted()) {
            $errors = $taxOptionsFormHandler->save($taxOptionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_taxes_index');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getTaxOptionsFormHandler()
    {
        return $this->get('prestashop.admin.tax_options.form_handler');
    }
}
