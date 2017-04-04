<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Entity\Repository\StockRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockController
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var StockRepository
     */
    public $stockRepository;

    /**
     * @var QueryParamsCollection
     */
    public $queryParams;

    /**
     * @var MovementsCollection;
     */
    public $movements;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listProductsAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stock = $this->stockRepository->getData($queryParamsCollection);
        $totalPages = $this->stockRepository->countPages($queryParamsCollection);

        return new JsonResponse($stock, 200, array('Total-Pages' => $totalPages));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editProductAction(Request $request)
    {
        try {
            $this->guardAgainstMissingDeltaParameter($request);
            $delta = $request->request->getInt('delta');
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        $productIdentity = ProductIdentity::fromArray(array(
            'product_id' => $request->attributes->get('productId'),
            'combination_id' => $request->attributes->get('combinationId', 0)
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
     * @return JsonResponse
     */
    public function bulkEditProductsAction(Request $request)
    {
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
     * @param $content
     * @return mixed
     */
    private function guardAgainstInvalidJsonBody($content)
    {
        $decodedContent = json_decode($content, true);

        $jsonLastError = json_last_error();
        if ($jsonLastError !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('The request body should be a valid JSON content');
        }

        return $decodedContent;
    }

    /**
     * @param HttpException $exception
     * @return JsonResponse
     */
    private function handleException(HttpException $exception)
    {
        $this->logger->info($exception->getMessage());

        return new JsonResponse(array('error' => $exception->getMessage()), $exception->getStatusCode());
    }

    /**
     * @param Request $request
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
     * @return mixed
     */
    private function guardAgainstMissingParametersInBulkEditionRequest(Request $request)
    {
        $decodedContent = $this->guardAgainstInvalidJsonBody($request->getContent());

        $message = 'Each item of JSON-encoded array in the request body should contain ' .
            'a product id ("product_id"), a quantity delta ("delta"). '.
            'The item of index #%d is invalid.';

        array_walk($decodedContent, function ($item, $index) use ($message) {
            if (!array_key_exists('product_id', $item) || !array_key_exists('delta', $item)) {
                throw new BadRequestHttpException(sprintf($message, $index));
            }
        });
    }
}
