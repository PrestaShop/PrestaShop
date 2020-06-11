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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Command\DeleteProductFromMerchandiseReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetMerchandiseReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\EditableMerchandiseReturn;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnProductsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
     * @throws Exception
     */
    public function indexAction(Request $request, MerchandiseReturnFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return');

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
            'merchandiseReturnsGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
            'merchandiseReturnsOptionsForm' => $optionsForm->createView(),
        ]);
    }

    /**
     *
     * @todo so I still have an issue with this not saving properly. Issue is that it's first POST request which redirects to GET request.
     * As far as I can see this is caused by the fact that Symfony/PrestaShop deciding that my POST request is in fact SearchGridAction
     * At this point I am stuck. If i left the redirection somewhere then I can't find it. Maybe the fact that form and grid are in one page?
     * But then options form + grid wouldn't work either.
     *
     * This is caused
     * Edit existing merchandise return
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_returns_index"
     * )
     *
     * @param int $merchandiseReturnId
     * @param Request $request
     * @param MerchandiseReturnProductsFilters $filters
     *
     * @return Response
     */
    public function editAction(int $merchandiseReturnId, MerchandiseReturnProductsFilters $filters, Request $request): Response
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.merchandise_return_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.merchandise_return_form_handler');
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return_products');

        try {
            /** @var EditableMerchandiseReturn $editableMerchandiseReturn */
            $editableMerchandiseReturn = $this->getQueryBus()->handle(
                new GetMerchandiseReturnForEditing(
                    $merchandiseReturnId
                )
            );

            $form = $formBuilder->getFormFor($merchandiseReturnId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($merchandiseReturnId, $form);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            }
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
            'merchandiseReturnsProductsGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_merchandise_returns_index")
     *
     * @param Request $request
     * @param int $merchandiseReturnId
     * @param int $merchandiseReturnDetailId
     * @param int $customizationId
     *
     * @return RedirectResponse
     */
    public function deleteProductAction(Request $request, int $merchandiseReturnId, int $merchandiseReturnDetailId, int $customizationId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(
                new DeleteProductFromMerchandiseReturnCommand($merchandiseReturnId, $merchandiseReturnDetailId, $customizationId)
            );

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $request->query->has('redirectUrl') ?
            $this->redirect($request->query->get('redirectUrl')) :
            $this->redirectToRoute(
                'admin_merchandise_returns_edit',
                [
                    'merchandiseReturnId' => $merchandiseReturnId,
                ]
            );
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
    private function getErrorMessages(): array
    {
        return [
            MerchandiseReturnConstraintException::class => [
                MerchandiseReturnConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
