<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\SearchShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller to manage shops.
 */
class ShopController extends PrestaShopAdminController
{
    /**
     * The term comes from the query parameter: in the path, a "/" splits the route and the web server refuses its
     * encoded form. The path parameter is kept for the callers that still use it.
     */
    public function searchAction(Request $request, string $searchTerm = ''): JsonResponse
    {
        $searchTerm = $request->query->getString('query', $searchTerm);

        try {
            $result = [];
            $result['shops'] = $this->dispatchQuery(new SearchShops($searchTerm));
            $statusCode = empty($result['shops']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        } catch (SearchShopException) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e)],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($result['shops'], $statusCode);
    }
}
