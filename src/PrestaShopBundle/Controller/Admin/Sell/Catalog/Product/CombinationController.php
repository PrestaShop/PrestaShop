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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationFromListingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CombinationController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getListAction(int $productId, Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('limit');
        $page = (int) $request->query->get('page');

        $combinationsList = $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $productId,
            $this->getContextLangId(),
            $limit ?? null,
            $page ?? null
        ));

        return $this->json($this->formatResponse($combinationsList));
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $combinationId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateImpactOnPriceAction(int $combinationId, Request $request): JsonResponse
    {
        $impactOnPrice = $request->request->get('impactOnPrice');

        if (!$impactOnPrice) {
            return $this->json(
                ['message' => 'Missing impactOnPrice'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new UpdateCombinationFromListingCommand($combinationId);
        $command->setImpactOnPrice($impactOnPrice);

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getFallbackErrorMessage(get_class($e), $e->getCode(), $e->getMessage())],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $combinationId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateQuantityAction(int $combinationId, Request $request): JsonResponse
    {
        $quantity = $request->request->get('quantity');

        if (!$quantity) {
            return $this->json(
                ['message' => 'Missing quantity'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new UpdateCombinationFromListingCommand($combinationId);
        $command->setQuantity($quantity);

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getFallbackErrorMessage(get_class($e), $e->getCode(), $e->getMessage())],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $combinationId
     *
     * @return JsonResponse
     */
    public function markAsDefaultAction(int $combinationId): JsonResponse
    {
        $command = new UpdateCombinationFromListingCommand($combinationId);
        $command->setDefault(true);

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getFallbackErrorMessage(get_class($e), $e->getCode(), $e->getMessage())],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([]);
    }

    /**
     * @param CombinationListForEditing $combinationListForEditing
     *
     * @return array<string, array<string, string|int|bool>>
     */
    private function formatResponse(CombinationListForEditing $combinationListForEditing): array
    {
        $data = [
            'combinations' => [],
            'total' => $combinationListForEditing->getTotalCombinationsCount(),
        ];
        foreach ($combinationListForEditing->getCombinations() as $combination) {
            $data['combinations'][] = [
                'id' => $combination->getCombinationId(),
                'isSelected' => false,
                'name' => $combination->getCombinationName(),
                //@todo: do I need to get image link here or in QueryResult?
                'impactOnPrice' => (string) $combination->getImpactOnPrice(),
                //@todo: calculate final price. Need a service to be used in formData provider and here
                'finalPriceTe' => 0,
                'quantity' => $combination->getQuantity(),
                'isDefault' => $combination->isDefault(),
            ];
        }

        return $data;
    }
}
