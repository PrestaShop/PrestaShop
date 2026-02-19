<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\SearchCartRules;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Cart rules (a.k.a cart discounts/vouchers) actions in Back Office
 */
class CartRuleController extends PrestaShopAdminController
{
    /**
     * Searches for cart rules by provided search phrase
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function searchAction(Request $request): JsonResponse
    {
        $searchPhrase = $request->query->get('search_phrase');
        $cartRules = [];

        if ($searchPhrase) {
            try {
                $cartRules = $this->dispatchQuery(new SearchCartRules($searchPhrase));
            } catch (Exception $e) {
                return $this->json(
                    ['message' => $this->getErrorMessageForException($e, [])],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }

        return $this->json([
            'cart_rules' => $cartRules,
        ]);
    }
}
