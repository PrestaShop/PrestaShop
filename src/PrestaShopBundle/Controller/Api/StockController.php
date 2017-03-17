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

use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listProductsAction(Request $request)
    {
        $productStockRepository = $this->get('prestashop.core.api.product_stock.repository');
        $queryParamsCollection = $this->get('prestashop.core.api.query_params_collection');

        $queryParamsCollection = $queryParamsCollection->fromRequest($request);
        $stockOverviewColumns = $productStockRepository->getProducts($queryParamsCollection);

        return new JsonResponse($stockOverviewColumns);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editProductAction(Request $request)
    {
        try {
            $this->guardAgainstMissingDeltaParameter($request);
            $delta = (int)$request->request->get('delta');
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        $productIdentity = ProductIdentity::fromArray(array(
            'product_id' => $request->attributes->get('productId'),
            'combination_id' => $request->attributes->get('combinationId', 0)
        ));

        $productStockRepository = $this->get('prestashop.core.api.product_stock.repository');

        try {
            $product = $productStockRepository->updateProductQuantity($productIdentity, $delta);
        } catch (ProductNotFoundException $exception) {
            return $this->handleException($exception);
        }

        return new JsonResponse($product);
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
        $decodedContent = json_decode($content, true);

        $jsonLastError = json_last_error();
        if ($jsonLastError !== JSON_ERROR_NONE || !array_key_exists('delta', $decodedContent)) {
            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        return $decodedContent;
    }

    /**
     * @param HttpException $exception
     * @return JsonResponse
     */
    private function handleException(HttpException $exception)
    {
        $this->get('logger')->info($exception->getMessage());

        return new JsonResponse(array('error' => $exception->getMessage()), $exception->getStatusCode());
    }
}
