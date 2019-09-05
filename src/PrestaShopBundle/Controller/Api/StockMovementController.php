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

use PrestaShopBundle\Api\QueryStockMovementParamsCollection;
use PrestaShopBundle\Entity\Repository\StockMovementRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StockMovementController extends ApiController
{
    /**
     * @var StockMovementRepository
     */
    public $stockMovementRepository;

    /**
     * @var QueryStockMovementParamsCollection
     */
    public $queryParams;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listMovementsAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stockMovement = $this->stockMovementRepository->getData($queryParamsCollection);
        $totalPages = $this->stockMovementRepository->countPages($queryParamsCollection);

        return $this->jsonResponse($stockMovement, $request, $queryParamsCollection, 200, array('Total-Pages' => $totalPages));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listMovementsEmployeesAction(Request $request)
    {
        return $this->jsonResponse($this->stockMovementRepository->getEmployees(), $request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listMovementsTypesAction(Request $request)
    {
        $grouped = (bool) $request->get('grouped');

        return $this->jsonResponse($this->stockMovementRepository->getTypes($grouped), $request);
    }
}
