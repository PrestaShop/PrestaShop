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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
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
     * Used to validate connected user authorizations.
     */
    private const PRODUCT_CONTROLLER_PERMISSION = 'ADMINPRODUCTS_';

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
            ProductConstraintException::class => [
                ProductConstraintException::INVALID_PRICE => $this->trans(
                    'Product price is invalid',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
