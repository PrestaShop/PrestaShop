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
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetEditableSpecificPricesList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceListForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecificPriceController extends FrameworkBundleAdminController
{
    public function listAction(int $productId): JsonResponse
    {
        $specificPricesList = $this->getQueryBus()->handle(new GetEditableSpecificPricesList($productId));

        return $this->json(['specificPrices' => $this->formatSpecificPricesList($specificPricesList)]);
    }

    public function createAction(Request $request, int $productId): Response
    {
        $form = $this->getFormBuilder()->getForm(['product_id' => $productId]);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Creation successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                    'specificPriceId' => $result->getIdentifiableObjectId(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/create.html.twig', [
            'specificPriceForm' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, int $specificPriceId): Response
    {
        $form = $this->getFormBuilder()->getFormFor($specificPriceId);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/edit.html.twig', [
            'specificPriceForm' => $form->createView(),
            'specificPriceId' => $specificPriceId,
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array<string, mixed>
     */
    private function getErrorMessages(): array
    {
        return [
            SpecificPriceConstraintException::class => [
                SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT => $this->trans(
                    'A specific price already exists for these parameters.',
                    'Admin.Catalog.Notification'
                ),
            ],
        ];
    }

    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.specific_price_form_builder');
    }

    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.specific_price_form_handler');
    }

    private function formatSpecificPricesList(SpecificPriceListForEditing $specificPriceListForEditing): array
    {
        $list = [];
        foreach ($specificPriceListForEditing->getSpecificPrices() as $specificPriceForEditing) {
            $list[] = [
                'id' => $specificPriceForEditing->getSpecificPriceId(),
                //@todo: missing combination id in specificPriceForEditing
                'combination' => 'All combinations',
                //@todo: Name instead of id already in query handler?
                'currency' => $specificPriceForEditing->getCurrencyId(),
                'country' => $specificPriceForEditing->getCountryId(),
                'group' => $specificPriceForEditing->getGroupId(),
                'customer' => $specificPriceForEditing->getCustomerId(),
                //@todo: format with currency icon?
                'price' => (string) $specificPriceForEditing->getPrice(),
                //@todo: format impact based on reduction type (% or currency icon)
                'impact' => $specificPriceForEditing->getReductionAmount(),
                //@todo: format period from $from - to $to
                'period' => $specificPriceForEditing->getDateTimeFrom(),
                'fromQuantity' => $specificPriceForEditing->getFromQuantity()
            ];
        }

        return $list;
    }
}
