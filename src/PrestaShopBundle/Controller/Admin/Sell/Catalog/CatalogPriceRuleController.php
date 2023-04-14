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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\BulkDeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\DeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CannotDeleteCatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CannotUpdateCatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleListForProduct;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CatalogPriceRuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\CatalogPriceRuleFilters;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Discounts > Catalog Price Rules page
 */
class CatalogPriceRuleController extends FrameworkBundleAdminController
{
    private const UNSPECIFIED_VALUE_FORMAT = '--';

    /**
     * Displays catalog price rule listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CatalogPriceRuleFilters $catalogPriceRuleFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        CatalogPriceRuleFilters $catalogPriceRuleFilters
    ) {
        $catalogPriceRuleGridFactory = $this->get('prestashop.core.grid.grid_factory.catalog_price_rule');
        $catalogPriceRuleGrid = $catalogPriceRuleGridFactory->getGrid($catalogPriceRuleFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'catalogPriceRuleGrid' => $this->presentGrid($catalogPriceRuleGrid),
        ]);
    }

    /**
     * Retrieves catalog prices rules for product.
     *
     * @AdminSecurity("is_granted('read', 'AdminProducts') || is_granted('read', 'AdminSpecificPriceRule')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listForProductAction(Request $request, int $productId): JsonResponse
    {
        $catalogPriceRuleList = $this->getQueryBus()->handle(
            new GetCatalogPriceRuleListForProduct(
                $productId,
                $this->getContextLangId(),
                $request->query->getInt('limit') ?: null,
                $request->query->getInt('offset') ?: null
            )
        );

        return $this->json(
            [
                'catalogPriceRules' => $this->formatCatalogPriceRule($catalogPriceRuleList),
                'total' => $catalogPriceRuleList->getTotalCount(),
            ]
        );
    }

    /**
     * Provides filters functionality.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.catalog_price_rule'),
            $request,
            CatalogPriceRuleGridDefinitionFactory::GRID_ID,
            'admin_catalog_price_rules_index'
        );
    }

    /**
     * Deletes catalog price rule
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_catalog_price_rules_index")
     * @DemoRestricted(redirectRoute="admin_catalog_price_rules_index")
     *
     * @param int|string $catalogPriceRuleId
     *
     * @return RedirectResponse
     */
    public function deleteAction($catalogPriceRuleId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteCatalogPriceRuleCommand((int) $catalogPriceRuleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_catalog_price_rules_index');
    }

    /**
     * Deletes catalogPriceRules on bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_catalog_price_rules_index")
     * @DemoRestricted(redirectRoute="admin_catalog_price_rules_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $catalogPriceRuleIds = $this->getBulkCatalogPriceRulesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCatalogPriceRuleCommand($catalogPriceRuleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_catalog_price_rules_index');
    }

    /**
     * Show & process catalog price rule creation.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $catalogPriceRuleForm = $this->getFormBuilder()->getForm();
        $catalogPriceRuleForm->handleRequest($request);
        $result = $this->getFormHandler()->handle($catalogPriceRuleForm);

        try {
            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_catalog_price_rules_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'catalogPriceRuleForm' => $catalogPriceRuleForm->createView(),
            'layoutTitle' => $this->trans('New catalog rule', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Show & process catalog price rule editing.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $catalogPriceRuleId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, int $catalogPriceRuleId): Response
    {
        $catalogPriceRuleId = (int) $catalogPriceRuleId;

        try {
            /** @var EditableCatalogPriceRule $editableCatalogPriceRule */
            $editableCatalogPriceRule = $this->getQueryBus()->handle(new GetCatalogPriceRuleForEditing($catalogPriceRuleId));

            $catalogPriceRuleForm = $this->getFormBuilder()->getFormFor($catalogPriceRuleId);
            $catalogPriceRuleForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor($catalogPriceRuleId, $catalogPriceRuleForm);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_catalog_price_rules_index');
        }

        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_catalog_price_rules_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'catalogPriceRuleForm' => $catalogPriceRuleForm->createView(),
            'catalogPriceRuleName' => $editableCatalogPriceRule->getName(),
            'layoutTitle' => $this->trans(
                'Editing price rule %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $editableCatalogPriceRule->getName(),
                ]
            ),
        ]);
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            CannotDeleteCatalogPriceRuleException::class => [
                CannotDeleteCatalogPriceRuleException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteCatalogPriceRuleException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotUpdateCatalogPriceRuleException::class => $this->trans(
                'An error occurred while updating an object.',
                'Admin.Notifications.Error'
            ),
            CatalogPriceRuleNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * Provides catalog price rule ids from request of bulk action
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCatalogPriceRulesFromRequest(Request $request)
    {
        $catalogPriceRuleIds = $request->request->all('catalog_price_rule_bulk');

        foreach ($catalogPriceRuleIds as &$catalogPriceRuleId) {
            $catalogPriceRuleId = (int) $catalogPriceRuleId;
        }

        return $catalogPriceRuleIds;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.catalog_price_rule_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.catalog_price_rule_form_builder');
    }

    /**
     * @param CatalogPriceRuleList $catalogPriceRuleList
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatCatalogPriceRule(CatalogPriceRuleList $catalogPriceRuleList): array
    {
        $list = [];
        foreach ($catalogPriceRuleList->getCatalogPriceRules() as $catalogPriceRule) {
            $list[] = [
                'id' => $catalogPriceRule->getCatalogPriceRuleId(),
                'shop' => $catalogPriceRule->getShopName() ?? $this->trans('All stores', 'Admin.Global'),
                'currency' => $catalogPriceRule->getCurrencyName() ?? $this->trans('All currencies', 'Admin.Global'),
                'country' => $catalogPriceRule->getCountryName() ?? $this->trans('All countries', 'Admin.Global'),
                'group' => $catalogPriceRule->getGroupName() ?? $this->trans('All groups', 'Admin.Global'),
                'name' => $catalogPriceRule->getCatalogPriceRuleName(),
                'fromQuantity' => $catalogPriceRule->getFromQuantity(),
                'impact' => $this->formatImpact(
                    $catalogPriceRule->getReductionType(),
                    $catalogPriceRule->getReduction(),
                    $catalogPriceRule->getCurrencyIso() ?: $this->getContextCurrencyIso(),
                    $catalogPriceRule->isTaxIncluded()
                ),
                'startDate' => $catalogPriceRule->getDateStart()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'endDate' => $catalogPriceRule->getDateEnd()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            ];
        }

        return $list;
    }

    /**
     * @param string $reductionType
     * @param DecimalNumber $reductionValue
     * @param string $currencyIsoCode
     *
     * @return string
     */
    private function formatImpact(
        string $reductionType,
        DecimalNumber $reductionValue,
        string $currencyIsoCode,
        bool $taxIncl
    ): string {
        if ($reductionValue->equalsZero()) {
            return self::UNSPECIFIED_VALUE_FORMAT;
        }

        $reductionValue = $reductionValue->toNegative();

        $locale = $this->getContextLocale();
        if ($reductionType === Reduction::TYPE_AMOUNT) {
            $price = $locale->formatPrice((string) $reductionValue, $currencyIsoCode);
            if ($taxIncl) {
                return $this->trans('%price% (tax incl.)', 'Admin.Catalog.Feature', ['%price%' => $price]);
            }

            return $this->trans('%price% (tax excl.)', 'Admin.Catalog.Feature', ['%price%' => $price]);
        }

        return sprintf('%s %%', (string) $reductionValue);
    }
}
