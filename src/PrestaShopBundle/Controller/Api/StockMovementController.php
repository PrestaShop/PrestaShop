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
use PrestaShopBundle\Entity\Repository\StockMovementRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockMovementController
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var StockMovementRepository
     */
    public $stockMovementRepository;

    /**
     * @var QueryParamsCollection
     */
    public $queryParams;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listMovementsAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stockMovement = $this->stockMovementRepository->getMovements($queryParamsCollection);
        $totalPages = $this->stockMovementRepository->countMovementPages($queryParamsCollection);

        return new JsonResponse($stockMovement, 200, array('Total-Pages' => $totalPages));
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
}
