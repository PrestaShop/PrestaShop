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

namespace PrestaShopBundle\Controller\Api;

use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Api\QueryStockParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Entity\Repository\StockRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class StockController extends ApiController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly StockRepository $stockRepository,
        private readonly QueryStockParamsCollection $queryParams,
        private readonly MovementsCollection $movements,
    ) {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listProductsAction(Request $request)
    {
        if (!$this->isGranted(Permission::READ, $request->get('_legacy_controller'))) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        try {
            $queryParams = $request->query->all();

            if (isset($queryParams['keywords']) && !is_array($queryParams['keywords'])) {
                // 'keywords' exists in the parameters and is not array, so it must be converted into an array
                $queryParams['keywords'] = explode(',', $queryParams['keywords']);
                $request->query->replace($queryParams);
            }

            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stock = [
            'info' => [
                'edit_bulk_url' => $this->container->get('router')->generate('api_stock_bulk_edit_products'),
            ],
            'data' => $this->stockRepository->getData($queryParamsCollection),
        ];
        $totalPages = $this->stockRepository->countPages($queryParamsCollection);

        return $this->jsonResponse($stock, $request, $queryParamsCollection, 200, ['Total-Pages' => $totalPages]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editProductAction(Request $request)
    {
        if (!$this->isGranted(Permission::UPDATE, $request->get('_legacy_controller'))) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->guardAgainstMissingDeltaParameter($request);
            $delta = $request->request->getInt('delta');
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        $productIdentity = ProductIdentity::fromArray([
            'product_id' => $request->attributes->get('productId'),
            'combination_id' => $request->attributes->get('combinationId', 0),
        ]);

        try {
            $movement = new Movement($productIdentity, $delta);
            $product = $this->stockRepository->updateStock($movement);
        } catch (ProductNotFoundException $exception) {
            return $this->handleException($exception);
        }

        return new JsonResponse($product);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkEditProductsAction(Request $request)
    {
        if (!$this->isGranted(Permission::UPDATE, $request->get('_legacy_controller'))) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->guardAgainstInvalidBulkEditionRequest($request);
            $stockMovementsParams = json_decode($request->getContent(), true);
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        $movementsCollection = $this->movements->fromArray($stockMovementsParams);

        try {
            $products = $this->stockRepository->bulkUpdateStock($movementsCollection);
        } catch (ProductNotFoundException $exception) {
            return $this->handleException($exception);
        }

        return new JsonResponse($products);
    }

    /**
     * @param Request $request
     *
     * @return CsvResponse|JsonResponse
     */
    public function listProductsExportAction(Request $request)
    {
        if (!$this->isGranted(Permission::READ, $request->get('_legacy_controller'))) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $dataCallback = function ($page, $limit) use ($queryParamsCollection) {
            return $this->stockRepository->getDataExport($page, $limit, $queryParamsCollection);
        };

        // headers columns
        $headersData = [
            'product_id' => 'Product ID',
            'combination_id' => 'Combination ID',
            'product_reference' => $this->translator->trans('Product reference', [], 'Admin.Advparameters.Feature'),
            'combination_reference' => $this->translator->trans('Combination reference', [], 'Admin.Advparameters.Feature'),
            'product_name' => $this->translator->trans('Product name', [], 'Admin.Catalog.Feature'),
            'combination_name' => $this->translator->trans('Combination name', [], 'Admin.Catalog.Feature'),
            'supplier_name' => $this->translator->trans('Supplier', [], 'Admin.Global'),
            'active' => $this->translator->trans('Status', [], 'Admin.Global'),
            'product_physical_quantity' => $this->translator->trans('Physical quantity', [], 'Admin.Catalog.Feature'),
            'product_reserved_quantity' => $this->translator->trans('Reserved quantity', [], 'Admin.Catalog.Feature'),
            'product_available_quantity' => $this->translator->trans('Available quantity', [], 'Admin.Catalog.Feature'),
            'product_low_stock_threshold' => $this->translator->trans('Low stock level', [], 'Admin.Catalog.Feature'),
            'product_low_stock_alert' => $this->translator->trans('Send me an email when the quantity is below or equals this level', [], 'Admin.Catalog.Feature'),
        ];

        return (new CsvResponse())
            ->setData($dataCallback)
            ->setHeadersData($headersData)
            ->setLimit(10000)
            ->setFileName('stock_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    private function guardAgainstMissingDeltaParameter(Request $request): void
    {
        $message = 'The "delta" parameter is required';

        $content = $request->getContent();
        if (strlen($content) > 0) {
            $decodedContent = $this->guardAgainstInvalidRequestContent($content, $message);
            $request->request->set('delta', $decodedContent['delta']);
        }

        if (!$request->request->has('delta')) {
            throw new BadRequestHttpException($message);
        }
    }

    /**
     * @param string $content
     * @param string $message
     *
     * @return array
     */
    private function guardAgainstInvalidRequestContent(string $content, string $message): array
    {
        $decodedContent = $this->guardAgainstInvalidJsonBody($content);

        if (!array_key_exists('delta', $decodedContent)) {
            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        return $decodedContent;
    }

    /**
     * @param Request $request
     */
    private function guardAgainstInvalidBulkEditionRequest(Request $request): void
    {
        if (strlen($request->getContent()) == 0) {
            $message = 'The request body should contain a JSON-encoded array of product identifiers and deltas';

            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        $this->guardAgainstMissingParametersInBulkEditionRequest($request);
    }

    /**
     * @param Request $request
     */
    private function guardAgainstMissingParametersInBulkEditionRequest(Request $request): void
    {
        $decodedContent = $this->guardAgainstInvalidJsonBody($request->getContent());

        $messageMissingParameters = 'Each item of JSON-encoded array in the request body should contain ' .
            'a product id ("product_id"), a quantity delta ("delta"). ' .
            'The item of index #%d is invalid.';
        $messageEmptyData = $this->translator->trans(
            'Value cannot be 0.',
            [],
            'Admin.Notifications.Error'
        );

        array_walk($decodedContent, function ($item, $index) use ($messageMissingParameters, $messageEmptyData) {
            if (!array_key_exists('product_id', $item) || !array_key_exists('delta', $item)) {
                throw new BadRequestHttpException(sprintf($messageMissingParameters, $index));
            }
            if ($item['delta'] == 0) {
                throw new BadRequestHttpException(sprintf($messageEmptyData, $index));
            }
        });
    }
}
