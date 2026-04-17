<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller to manage shops.
 */
class ShopController extends PrestaShopAdminController
{
    /**
     * Search for shops by query.
     *
     * @param string $searchTerm
     *
     * @return JsonResponse
     */
    public function searchAction(string $searchTerm): JsonResponse
    {
        try {
            $result = [];
            $result['shops'] = $this->dispatchQuery(new SearchShops((string) $searchTerm));
            $statusCode = empty($result['shops']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e)],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($result['shops'], $statusCode);
    }
}
