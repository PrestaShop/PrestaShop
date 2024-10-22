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

use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteAliasSearchTermCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\DeleteAliasSearchTermCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotDeleteAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\SearchForSearchTerm;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\AliasFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Search" page.
 */
class SearchAliasController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        AliasFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.alias')]
        GridFactoryInterface $aliasGridFactory,
    ): Response {
        $aliasGrid = $aliasGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/index.html.twig', [
            'aliasGrid' => $this->presentGrid($aliasGrid),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'desc' => $this->trans('Add new alias', [], 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                    'href' => $this->generateUrl('admin_search_alias_create'),
                ],
            ],
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAliasesForAssociationAction(Request $request): JsonResponse
    {
        try {
            /** @var string[] $searchTerms */
            $searchTerms = $this->dispatchQuery(new SearchForSearchTerm(
                $request->get('query', ''),
                (int) $request->get('limit', SearchForSearchTerm::DEFAULT_LIMIT)
            ));
        } catch (AliasConstraintException $e) {
            return $this->json([
                'message' => $this->getErrorMessageForException($e, []),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($searchTerms)) {
            return $this->json(['searchTerms' => []], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['searchTerms' => $searchTerms]);
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index')]
    public function deleteAction(string $searchTerm): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteAliasSearchTermCommand($searchTerm));
            $this->addFlash('success', $this->trans('Successful deletion.', [], 'Admin.Notifications.Success'));
        } catch (AliasException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_search_alias_index');
        }

        return $this->redirectToRoute('admin_search_alias_index');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $searchTerms = $request->request->all('alias_term_bulk');

        try {
            $this->dispatchCommand(new BulkDeleteAliasSearchTermCommand($searchTerms));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (AliasException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_search_alias_index');
    }

    public function getErrorMessages(): array
    {
        return [
            AliasConstraintException::class => [
                AliasConstraintException::INVALID_ID => $this->trans(
                    'Invalid alias ID.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
                AliasConstraintException::INVALID_ALIAS => $this->trans(
                    'Invalid alias.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
                AliasConstraintException::INVALID_SEARCH => $this->trans(
                    'Invalid search term.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
            ],
            AliasNotFoundException::class => $this->trans(
                'This alias does not exist.',
                [],
                'Admin.Shopparameters.Feature'
            ),
            CannotDeleteAliasException::class => $this->trans(
                'Failed to delete alias.',
                [],
                'Admin.Shopparameters.Feature'
            ),
        ];
    }
}
