<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetMerchandiseReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetMerchandiseReturnProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\EditableMerchandiseReturn;
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
     *     redirectRoute="admin_merchandise_returns_index"
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
     * Edit existing merchandise return
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_returns_index"
     * )
     *
     * @param int $merchandiseReturnId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $merchandiseReturnId, Request $request): Response
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.merchandise_return_form_builder');

       // $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_message_form_handler');

        try {
            /** @var EditableMerchandiseReturn $editableMerchandiseReturn */
            $editableMerchandiseReturn = $this->getQueryBus()->handle(
                new GetMerchandiseReturnForEditing(
                    $merchandiseReturnId,
                    $this->getContextLangId()
                )
            );

            $productsForViewing = $this->getQueryBus()->handle(
                new GetMerchandiseReturnProductsForViewing(
                    $merchandiseReturnId
                )
            );

            $form = $formBuilder->getFormFor($merchandiseReturnId);
//            $form->handleRequest($request);
//
//            $result = $formHandler->handleFor($merchandiseReturnId, $form);
//
//            if ($result->getIdentifiableObjectId()) {
//                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
//
//                return $this->redirectToRoute('admin_merchandise_returns_index');
//            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        if (!isset($form)) {
            return $this->redirectToRoute('admin_merchandise_returns_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => sprintf($this->trans('Return Merchandise Authorization (RMA) ', 'Admin.Actions')),
            'merchandiseReturnForm' => $form->createView(),
            'editableMerchandiseReturn' => $editableMerchandiseReturn,
            'merchandiseReturnProductsForViewing' => $productsForViewing
        ]);
    }

    /**
     * @return FormHandlerInterface
     */
    private function getOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.merchandise_return_options.form_handler');
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            MerchandiseReturnConstraintException::class => [
                MerchandiseReturnConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                )
            ],
        ];
    }
}
