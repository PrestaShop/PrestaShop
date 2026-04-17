<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Api\QueryStockMovementParamsCollection;
use PrestaShopBundle\Entity\Repository\StockMovementRepository;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function listMovementsAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
        } catch (InvalidPaginationParamsException $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        $stockMovement = $this->stockMovementRepository->getData($queryParamsCollection);
        $totalPages = $this->stockMovementRepository->countPages($queryParamsCollection);

        return $this->jsonResponse($stockMovement, $request, $queryParamsCollection, 200, ['Total-Pages' => $totalPages]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function listMovementsEmployeesAction(Request $request)
    {
        return $this->jsonResponse($this->stockMovementRepository->getEmployees(), $request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function listMovementsTypesAction(Request $request)
    {
        $grouped = (bool) $request->get('grouped');

        return $this->jsonResponse($this->stockMovementRepository->getTypes($grouped), $request);
    }
}
