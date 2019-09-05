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

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Api\QueryStockParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Entity\Repository\StockRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use PrestaShopBundle\Exception\ProductNotFoundException;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StockController extends ApiController
{
    /**
     * @var StockRepository
     */
    public $stockRepository;

    /**
     * @var QueryStockParamsCollection
     */
    public $queryParams;

    /**
     * @var MovementsCollection;
     */
    public $movements;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listProductsAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stock = array(
            'info' => array(
                'edit_bulk_url' => $this->container->get('router')->generate('api_stock_bulk_edit_products'),
            ),
            'data' => $this->stockRepository->getData($queryParamsCollection),
        );
        $totalPages = $this->stockRepository->countPages($queryParamsCollection);

        return $this->jsonResponse($stock, $request, $queryParamsCollection, 200, array('Total-Pages' => $totalPages));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editProductAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::UPDATE], $request->get('_legacy_controller'))) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->guardAgainstMissingDeltaParameter($request);
            $delta = $request->request->getInt('delta');
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        $productIdentity = ProductIdentity::fromArray(array(
            'product_id' => $request->attributes->get('productId'),
            'combination_id' => $request->attributes->get('combinationId', 0),
        ));

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
        if (!$this->isGranted([PageVoter::UPDATE], $request->get('_legacy_controller'))) {
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
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $dataCallback = function ($page, $limit) use ($queryParamsCollection) {
            return $this->stockRepository->getDataExport($page, $limit, $queryParamsCollection);
        };

        $translator = $this->container->get('translator');

        // headers columns
        $headersData = array(
            'product_id' => 'Product ID',
            'combination_id' => 'Combination ID',
            'product_reference' => $translator->trans('Product reference', array(), 'Admin.Advparameters.Feature'),
            'combination_reference' => $translator->trans('Combination reference', array(), 'Admin.Advparameters.Feature'),
            'product_name' => $translator->trans('Product name', array(), 'Admin.Catalog.Feature'),
            'combination_name' => $translator->trans('Combination name', array(), 'Admin.Catalog.Feature'),
            'supplier_name' => $translator->trans('Supplier', array(), 'Admin.Global'),
            'active' => $translator->trans('Status', array(), 'Admin.Global'),
            'product_physical_quantity' => $translator->trans('Physical quantity', array(), 'Admin.Catalog.Feature'),
            'product_reserved_quantity' => $translator->trans('Reserved quantity', array(), 'Admin.Catalog.Feature'),
            'product_available_quantity' => $translator->trans('Available quantity', array(), 'Admin.Catalog.Feature'),
            'product_low_stock_threshold' => $translator->trans('Low stock level', array(), 'Admin.Catalog.Feature'),
            'product_low_stock_alert' => $translator->trans('Send me an email when the quantity is below or equals this level', array(), 'Admin.Catalog.Feature'),
        );

        return (new CsvResponse())
            ->setData($dataCallback)
            ->setHeadersData($headersData)
            ->setLimit(10000)
            ->setFileName('stock_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    private function guardAgainstMissingDeltaParameter(Request $request)
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
     * @param $content
     * @param $message
     *
     * @return mixed
     */
    private function guardAgainstInvalidRequestContent($content, $message)
    {
        $decodedContent = $this->guardAgainstInvalidJsonBody($content);

        if (!array_key_exists('delta', $decodedContent)) {
            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        return $decodedContent;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function guardAgainstInvalidBulkEditionRequest(Request $request)
    {
        if (strlen($request->getContent()) == 0) {
            $message = 'The request body should contain a JSON-encoded array of product identifiers and deltas';

            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        $this->guardAgainstMissingParametersInBulkEditionRequest($request);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function guardAgainstMissingParametersInBulkEditionRequest(Request $request)
    {
        $decodedContent = $this->guardAgainstInvalidJsonBody($request->getContent());

        $message = 'Each item of JSON-encoded array in the request body should contain ' .
            'a product id ("product_id"), a quantity delta ("delta"). ' .
            'The item of index #%d is invalid.';

        array_walk($decodedContent, function ($item, $index) use ($message) {
            if (!array_key_exists('product_id', $item) || !array_key_exists('delta', $item) || $item['delta'] == 0) {
                throw new BadRequestHttpException(sprintf($message, $index));
            }
        });
    }
}
