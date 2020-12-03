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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MerchandiseReturnController responsible for "Sell > Customer Service > Merchandise Returns" page
 */
class MerchandiseReturnController extends FrameworkBundleAdminController
{
    /**
     * Render merchandise returns grid and options.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_return_index"
     * )
     *
     * @param Request $request
     * @param MerchandiseReturnFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, MerchandiseReturnFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        $optionsFormHandler = $this->getOptionsFormHandler();
        $optionsForm = $optionsFormHandler->getForm();
        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {
            $errors = $optionsFormHandler->save($optionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/index.html.twig', [
            'merchandiseReturnsGrid' => $gridPresenter->present($gridFactory->getGrid($filters)),
            'merchandiseReturnsOptionsForm' => $optionsForm->createView(),
        ]);
    }

    /**
     * @return FormHandlerInterface
     */
    private function getOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.merchandise_return_options.form_handler');
    }
}
