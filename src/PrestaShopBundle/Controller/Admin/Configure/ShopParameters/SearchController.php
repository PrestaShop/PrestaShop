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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\SearchForSearchTerm;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\SearchTerm;
use PrestaShop\PrestaShop\Core\Search\Filters\AliasFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Search" page.
 */
class SearchController extends FrameworkBundleAdminController
{
    /**
     * Shows index Search page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param AliasFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, AliasFilters $filters): Response
    {
        $aliasGridFactory = $this->get('prestashop.core.grid.factory.alias');
        $aliasGrid = $aliasGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/index.html.twig', [
            'aliasGrid' => $this->presentGrid($aliasGrid),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'desc' => $this->trans('Add new alias', 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                    'href' => $this->generateUrl('admin_search_index'), // @TODO change when add new alias route will be implemented
                ],
            ],
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAliasesForAssociationAction(Request $request): JsonResponse
    {
        try {
            /** @var SearchTerm[] $searchTerms */
            $searchTerms = $this->getQueryBus()->handle(new SearchForSearchTerm(
                $request->get('query', ''),
                (int) $request->get('limit', SearchForSearchTerm::DEFAULT_ALIAS_SEARCH_LIMIT)
            ));
        } catch (AliasConstraintException $e) {
            return $this->json([
                'message' => $this->getErrorMessage($e),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($searchTerms)) {
            return $this->json(['aliases' => []], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['aliases' => $searchTerms]);
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    private function getErrorMessage(Exception $e): string
    {
        return $this->getFallbackErrorMessage(
            get_class($e),
            $e->getCode(),
            $e->getMessage()
        );
    }
}
