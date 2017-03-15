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

use PrestaShopBundle\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $productStockRepository = $this->get('prestashop.core.api.product_stock.repository');
        $queryParamsCollection = $this->get('prestashop.core.api.query_params_collection');

        $queryParamsCollection = $queryParamsCollection->fromRequest($request);
        $stockOverviewColumns = $productStockRepository->getStockOverviewRows($queryParamsCollection);

        return new JsonResponse($stockOverviewColumns);
    }

    /**
     * @param $productId
     * @param Request $request
     * @return JsonResponse
     */
    public function editProductAction($productId, Request $request)
    {
        $quantity = $this->guardAgainstMissingQuantityParameter($request);
        $productId = (int) $productId;

        $productStockRepository = $this->get('prestashop.core.api.product_stock.repository');

        try {
            $product = $productStockRepository->updateProductQuantity($productId, $quantity);
        } catch (ProductNotFoundException $exception) {
            $this->get('logger')->info($exception->getMessage());

            return new JsonResponse(array('error' => $exception->getMessage()), 404);
        }

        return new JsonResponse($product);
    }

    /**
     * @param $productId
     * @param $productAttributeId
     * @param Request $request
     * @return JsonResponse
     */
    public function editProductCombinationAction($productId, $productAttributeId, Request $request)
    {
        $quantity = $this->guardAgainstMissingQuantityParameter($request);
        $productAttributeId = (int) $productAttributeId;
        $productId = (int) $productId;

        $productStockRepository = $this->get('prestashop.core.api.product_stock.repository');

        try {
            $product = $productStockRepository->updateProductCombinationQuantity(
                $productId,
                $productAttributeId,
                $quantity
            );
        } catch (ProductNotFoundException $exception) {
            $this->get('logger')->info($exception->getMessage());

            return new JsonResponse(array('error' => $exception->getMessage()), 404);
        }

        return new JsonResponse($product);
    }

    /**
     * @param Request $request
     * @return int
     */
    private function guardAgainstMissingQuantityParameter(Request $request)
    {
        if (!$request->request->has('quantity')) {
            throw new BadRequestHttpException('Missing "quantity" parameter');
        }

        return (int)$request->request->get('quantity');
ti    }
}
