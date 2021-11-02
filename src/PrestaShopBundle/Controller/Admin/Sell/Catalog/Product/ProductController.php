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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;
use PrestaShop\PrestaShop\Core\Exception\ProductException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\ProductDownload;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
            'layoutHeaderToolbarBtn' => $this->getProductToolbarButtons(),
            'help_link' => $this->generateSidebarLink('AdminProducts'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to create this.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if (!$this->isProductPageV2Enabled()) {
            $this->addFlashMessageProductV2IsDisabled();

            return $this->redirectToRoute('admin_product_new');
        }
        if ($this->get('prestashop.adapter.multistore_feature')->isUsed()) {
            return $this->renderDisableMultistorePage();
        }

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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return Response
     */
    public function editAction(Request $request, int $productId): Response
    {
        if (!$this->isProductPageV2Enabled()) {
            $this->addFlashMessageProductV2IsDisabled();

            return $this->redirectToRoute('admin_product_form', ['id' => $productId]);
        }

        if ($this->get('prestashop.adapter.multistore_feature')->isUsed()) {
            return $this->renderDisableMultistorePage($productId);
        }

        $productForm = $this->getProductFormBuilder()->getFormFor($productId, [], [
            'product_id' => $productId,
            // @todo: patch/partial update doesn't work good for now (especially multiple empty values) so we use POST for now
            // 'method' => Request::METHOD_PATCH,
            'method' => Request::METHOD_POST,
        ]);

        try {
            $productForm->handleRequest($request);

            $result = $this->getProductFormHandler()->handleFor($productId, $productForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                    return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $productId]);
                } else {
                    // Display root level errors with flash messages
                    foreach ($productForm->getErrors() as $error) {
                        $this->addFlash('error', sprintf(
                            '%s: %s',
                            $error->getOrigin()->getName(),
                            $error->getMessage()
                        ));
                    }
                }
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
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (ProductException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_products_v2_index');
    }

    /**
     * @return array
     */
    private function getProductToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_product_new'),
            'desc' => $this->trans('New product', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
        ];

        $toolbarButtons['list_v1'] = [
            'href' => $this->generateUrl('admin_product_catalog'),
            'desc' => $this->trans('Back to standard page', 'Admin.Catalog.Feature'),
            'class' => 'btn-outline-primary',
        ];

        return $toolbarButtons;
    }

    /**
     * Download the content of the virtual product.
     *
     * @param int $virtualProductFileId
     *
     * @return BinaryFileResponse
     */
    public function downloadVirtualFileAction(int $virtualProductFileId): BinaryFileResponse
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $download = $this->getDoctrine()
            ->getRepository(ProductDownload::class)
            ->findOneBy([
                'id' => $virtualProductFileId,
            ]);

        $response = new BinaryFileResponse(
            $configuration->get('_PS_DOWNLOAD_DIR_') . $download->getFilename()
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $download->getDisplayFilename()
        );

        return $response;
    }

    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param string $languageCode
     *
     * @return JsonResponse
     */
    public function searchAssociationsAction(Request $request, string $languageCode): JsonResponse
    {
        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $lang = $langRepository->getOneByLocaleOrIsoCode($languageCode);
        if (null === $lang) {
            return $this->json([
                'message' => sprintf(
                    'Invalid language code %s was used which matches no existing language in this shop.',
                    $languageCode
                ),
            ], Response::HTTP_BAD_REQUEST);
        }

        $shopId = $this->get('prestashop.adapter.shop.context')->getContextShopID();
        if (empty($shopId)) {
            $shopId = $this->get('prestashop.adapter.legacy.configuration')->getInt('PS_SHOP_DEFAULT');
        }

        try {
            /** @var ProductForAssociation[] $products */
            $products = $this->getQueryBus()->handle(new SearchProductsForAssociation(
                $request->get('query', ''),
                $lang->getId(),
                (int) $shopId
            ));
        } catch (ProductConstraintException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($products)) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->formatProductsForAssociation($products));
    }

    /**
     * @param ProductForAssociation[] $productsForAssociation
     *
     * @return array
     */
    private function formatProductsForAssociation(array $productsForAssociation): array
    {
        $productsData = [];
        foreach ($productsForAssociation as $productForAssociation) {
            $productName = $productForAssociation->getName();
            if (!empty($productForAssociation->getReference())) {
                $productName .= sprintf(' (ref: %s)', $productForAssociation->getReference());
            }

            $productsData[] = [
                'id' => $productForAssociation->getProductId(),
                'name' => $productName,
                'image' => $productForAssociation->getImageUrl(),
            ];
        }

        return $productsData;
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
        $categoryTreeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.category_tree_selector_form_builder');

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/edit.html.twig', [
            'categoryTreeSelectorForm' => $categoryTreeFormBuilder->getForm()->createView(),
            'showContentHeader' => false,
            'productForm' => $productForm->createView(),
            'statsLink' => $productId ? $this->getAdminLink('AdminStats', ['module' => 'statsproduct', 'id_product' => $productId]) : null,
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
            CannotDeleteProductException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteProductException::class => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
            ),
            ProductConstraintException::class => [
                ProductConstraintException::INVALID_PRICE => $this->trans(
                    'Product price is invalid',
                    'Admin.Notifications.Error'
                ),
                ProductConstraintException::INVALID_REDIRECT_TARGET => $this->trans(
                    'When redirecting towards a product you must select a target product.',
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

    /**
     * @return bool
     */
    private function isProductPageV2Enabled(): bool
    {
        $productPageV2FeatureFlag = $this->get('prestashop.core.feature_flags.modifier')
            ->getOneFeatureFlagByName(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);

        if (null === $productPageV2FeatureFlag) {
            return false;
        }

        return $productPageV2FeatureFlag->isEnabled();
    }

    private function addFlashMessageProductV2IsDisabled(): void
    {
        $this->addFlash(
            'warning',
            $this->trans(
                'The experimental product page is not enabled. To enable it, go to the %sExperimental Features%s page.',
                'Admin.Catalog.Notification',
                [
                    sprintf('<a href="%s">', $this->get('router')->generate('admin_feature_flags_index')),
                    '</a>',
                ]
            )
        );
    }

    /**
     * @param int|null $productId
     *
     * @return Response
     */
    private function renderDisableMultistorePage(int $productId = null): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/disabled.html.twig', [
            'errorMessage' => $this->trans(
                'This page is not yet compatible with the multistore feature. To access the page, please [1]disable the multistore feature[/1].',
                'Admin.Notifications.Info',
                [
                    '[1]' => sprintf('<a href="%s">', $this->get('router')->generate('admin_preferences')),
                    '[/1]' => '</a>',
                ]
            ),
            'standardPageUrl' => $this->generateUrl(
                !empty($productId) ? 'admin_product_form' : 'admin_product_new',
                !empty($productId) ? ['id' => $productId] : []
            ),
        ]);
    }
}
