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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CombinationController extends FrameworkBundleAdminController
{
    /**
     * @todo: security annotation
     *
     * @param int $productId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getListAction(int $productId, Request $request): JsonResponse
    {
        $limit = $request->request->get('limit');
        $page = $request->request->get('page');

        $combinationsList = $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $productId,
            $this->getContextLangId(),
            $limit,
            $page
        ));

        return $this->json($this->formatResponse($combinationsList));
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
                'is_selected' => false,
                'name' => $combination->getCombinationName(),
                //@todo: do I need to get image link here or in QueryResult?
                'impact_on_price' => (string) $combination->getImpactOnPrice(),
                //@todo: calculate final price. Need a service to be used in formData provider and here
                'final_price_te' => 0,
                'quantity' => $combination->getQuantity(),
                'is_default' => $combination->isDefault(),
            ];
        }

        return $data;
    }
}
