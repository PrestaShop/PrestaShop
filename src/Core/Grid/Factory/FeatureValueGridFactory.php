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

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\BulkDeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class allows adapting the feature value grid definition with dynamic values
 * which depends on values inside search filters (the name and actions that needs featureId and/or languageId)
 */
class FeatureValueGridFactory extends GridFactory
{
    use BulkDeleteActionTrait;

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param GridDataFactoryInterface $dataFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FeatureRepository $featureRepository
     *
     * @todo: after following ADR https://github.com/PrestaShop/ADR/pull/33,
     *        the Addapter/FeatureRepository usage should be replaced by interface
     *        and FeatureValueGridFactory should be removed from phpstan-disallowed-calls.neon "allowIn" section
     */
    public function __construct(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataFactoryInterface $dataFactory,
        GridFilterFormFactoryInterface $filterFormFactory,
        HookDispatcherInterface $hookDispatcher,
        protected readonly FeatureRepository $featureRepository,
        protected readonly TranslatorInterface $translator
    ) {
        parent::__construct($definitionFactory, $dataFactory, $filterFormFactory, $hookDispatcher);
    }

    public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface
    {
        if (!$searchCriteria instanceof FeatureValueFilters) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria. Expected "%s"', FeatureValueFilters::class));
        }

        /** @var GridDefinition $definition */
        $definition = $this->definitionFactory->getDefinition();
        $this->modifyDefinition($definition, $searchCriteria);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridDefinitionModifier', [
            'definition' => $definition,
        ]);

        $data = $this->dataFactory->getData($searchCriteria);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridDataModifier', [
            'data' => &$data,
        ]);

        $filterForm = $this->filterFormFactory->create($definition);
        $filterForm->setData($searchCriteria->getFilters());

        return new Grid(
            $definition,
            $data,
            $searchCriteria,
            $filterForm
        );
    }

    /**
     * Some modifications are needed in order to fill some required dynamic values coming from request (which are in filters like the $featureId)
     *
     * @param GridDefinition $definition
     * @param FeatureValueFilters $featureValueFilters
     *
     * @return void
     */
    protected function modifyDefinition(GridDefinition $definition, FeatureValueFilters $featureValueFilters): void
    {
        $definition->setName($this->featureRepository->getFeatureName(
            new FeatureId($featureValueFilters->getFeatureId()),
            new LanguageId($featureValueFilters->getLanguageId())
        ));

        $this->addFilters($definition, $featureValueFilters);
        $this->addGridActions($definition, $featureValueFilters);
        $this->addBulkActions($definition, $featureValueFilters);
    }

    /**
     * Add filter rows which requires dynamic values from request such as $featureId.
     *
     * @param GridDefinition $definition
     * @param FeatureValueFilters $featureValueFilters
     *
     * @return void
     */
    protected function addFilters(GridDefinition $definition, FeatureValueFilters $featureValueFilters): void
    {
        $definition->getFilters()->add((new Filter('actions', SearchAndResetType::class))
            // these action options are added in FeatureValueGridFactory, because they are dynamic and depend on filters
            ->setAssociatedColumn('actions')
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => $definition->getId(),
                ],
                'redirect_route' => 'admin_feature_values_index',
                'redirect_route_params' => [
                    'featureId' => $featureValueFilters->getFeatureId(),
                ],
            ])
        );
    }

    /**
     * Most of these actions could have been added statically in definition factory, but the export action requires
     * $featureId which comes from filters, therefore to maintain actions order and avoid complication we fill all of those actions here.
     *
     * @param GridDefinition $definition
     * @param FeatureValueFilters $featureValueFilters
     *
     * @return void
     */
    protected function addGridActions(GridDefinition $definition, FeatureValueFilters $featureValueFilters): void
    {
        $definition->getGridActions()
            ->add((new LinkGridAction('import'))
            ->setName($this->translator->trans('Import', [], 'Admin.Actions'))
            ->setIcon('cloud_upload')
            ->setOptions([
                'route' => 'admin_import',
                'route_params' => [
                    'import_type' => 'features',
                ],
            ])
            )
            ->add((new LinkGridAction('export'))
            ->setName($this->translator->trans('Export', [], 'Admin.Actions'))
            ->setIcon('cloud_download')
            ->setOptions([
                'route' => 'admin_feature_values_export',
                'route_params' => [
                    'featureId' => $featureValueFilters->getFeatureId(),
                ],
            ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
            ->setName($this->translator->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
            ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
            ->setName($this->translator->trans('Show SQL query', [], 'Admin.Actions'))
            ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
            ->setName($this->translator->trans('Export to SQL Manager', [], 'Admin.Actions'))
            ->setIcon('storage')
            );
    }

    /**
     * Adds bulk actions which requires featureId value from filters
     *
     * @param GridDefinition $definition
     * @param FeatureValueFilters $featureValueFilters
     *
     * @return void
     */
    protected function addBulkActions(GridDefinition $definition, FeatureValueFilters $featureValueFilters): void
    {
        $definition->getBulkActions()->add($this->buildBulkDeleteAction('admin_feature_values_bulk_delete', [
            'route_params' => [
                'featureId' => $featureValueFilters->getFeatureId(),
            ],
        ]));
    }

    protected function trans($id, array $options, $domain): string
    {
        return $this->translator->trans($id, $options, $domain);
    }
}
