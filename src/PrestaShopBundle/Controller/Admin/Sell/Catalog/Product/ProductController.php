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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Product\Combination\CombinationListType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the Product pages using the Symfony architecture:
 * - product list (display, search)
 * - product form (creation, edition)
 * - ...
 *
 * Some component displayed in this form are based on ajax request which might implemented
 * in another Controller.
 *
 * This controller is a re-migration of the initial ProductController which was the first
 * one to be migrated but doesn't meet the standards of the recently migrated controller.
 * The retro-compatibility is dropped for the legacy Admin pages, the former hook are no longer
 * managed for backward compatibility, new hooks need to be used in the modules, migration process
 * is detailed in the devdoc. (@todo add devdoc link when ready?)
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
     * Default number of combinations per page
     */
    private const DEFAULT_COMBINATIONS_NUMBER = 10;

    /**
     * Options used for the number of combinations per page
     */
    private const COMBINATIONS_PAGINATION_OPTIONS = [10, 20, 50, 100];

    /**
     * Used to validate connected user authorizations.
     */
    private const PRODUCT_CONTROLLER_PERMISSION = 'ADMINPRODUCTS_';

    /**
     * Shows products listing.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param ProductFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, ProductFilters $filters): Response
    {
        $productGridFactory = $this->get('prestashop.core.grid.factory.product');
        $productGrid = $productGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/index.html.twig', [
            'productGrid' => $this->presentGrid($productGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminProducts'),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.product'),
            $request,
            ProductGridDefinitionFactory::GRID_ID,
            'admin_products_v2_index'
        );
    }

    /**
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))", message="You do not have permission to create this.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $productForm = $this->getProductFormBuilder()->getForm();

        try {
            $productForm->handleRequest($request);

            $result = $this->getProductFormHandler()->handle($productForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $result->getIdentifiableObjectId()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->renderProductForm($productForm);
    }

    /**
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return Response
     */
    public function editAction(Request $request, int $productId): Response
    {
        $productForm = $this->getProductFormBuilder()->getFormFor($productId, [], [
            'product_id' => $productId,
            // @todo: patch/partial update doesn't work good for now (especially multiple empty values) so we use POST for now
            // 'method' => Request::METHOD_PATCH,
            'method' => Request::METHOD_POST,
        ]);

        try {
            $productForm->handleRequest($request);

            $result = $this->getProductFormHandler()->handleFor($productId, $productForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $productId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->renderProductForm($productForm, $productId);
    }

    /**
     * @param Request $request
     * @param int $productId
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $productId): Response
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand($productId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (ProductException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_products_v2_index');
    }

    /**
     * @param FormInterface $productForm
     * @param int|null $productId
     *
     * @return Response
     */
    private function renderProductForm(FormInterface $productForm, ?int $productId = null): Response
    {
        $shopContext = $this->get('prestashop.adapter.shop.context');
        $isMultiShopContext = count($shopContext->getContextListShopID()) > 1;

        $statsLink = null !== $productId ? $this->getAdminLink('AdminStats', ['module' => 'statsproduct', 'id_product' => $productId]) : null;

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/edit.html.twig', [
            'showContentHeader' => false,
            'productForm' => $productForm->createView(),
            'productId' => $productId,
            'statsLink' => $statsLink,
            'helpLink' => $this->generateSidebarLink('AdminProducts'),
            'isMultiShopContext' => $isMultiShopContext,
            'editable' => $this->isGranted(PageVoter::UPDATE, self::PRODUCT_CONTROLLER_PERMISSION),
            //@todo: hardcoded. Make configurable?
            'combinationLimitChoices' => self::COMBINATIONS_PAGINATION_OPTIONS,
            'combinationsLimit' => self::DEFAULT_COMBINATIONS_NUMBER,
            'combinationsForm' => $this->createForm(CombinationListType::class)->createView(),
        ]);
    }

    /**
     * Gets form builder.
     *
     * @return FormBuilderInterface
     */
    private function getProductFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.product_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getProductFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.product_form_handler');
    }

    /**
     * Gets an error by exception class and its code.
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        // @todo: all the constraint error messages are missing for now (see ProductConstraintException)
        return [
            CannotDeleteProductException::class => [
                CannotDeleteProductException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteProductException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            ProductConstraintException::class => [
                ProductConstraintException::INVALID_PRICE => $this->trans(
                    'Product price is invalid',
                    'Admin.Notifications.Error'
                ),
            ],
            DuplicateFeatureValueAssociationException::class => $this->trans(
                'You cannot associate the same feature value more than once.',
                'Admin.Notifications.Error'
            ),
            InvalidAssociatedFeatureException::class => $this->trans(
                'The selected value belongs to another feature.',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
