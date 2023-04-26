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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureValueGridDefinitionFactory extends AbstractConfigurableGridDefinitionFactory
{
    public const GRID_ID = 'feature_value';

    protected $options = [];

    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        FeatureRepository $featureRepository
    ) {
        parent::__construct($hookDispatcher);
        $this->featureRepository = $featureRepository;
    }

    public function configureOptions(array $options): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setRequired(['feature_id', 'language_id'])
            ->setAllowedTypes('feature_id', 'int')
            ->setAllowedTypes('language_id', 'int')
        ;

        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        //@todo: repository should be wrapped in interface (should be clear after ADR https://github.com/PrestaShop/ADR/pull/33 is finished)
        return $this->featureRepository->getFeatureName(
            new FeatureId($this->options['feature_id']),
            new LanguageId($this->options['language_id'])
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_feature_value'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_feature_value',
            ])
            )
            ->add((new DataColumn('value'))
            ->setName($this->trans('Value', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'value',
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            // @todo: actions will be added in dedicated PR
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add((new LinkGridAction('import'))
            ->setName($this->trans('Import', [], 'Admin.Actions'))
            ->setIcon('cloud_upload')
            ->setOptions([
                'route' => 'admin_import',
                'route_params' => [
                    'import_type' => 'features',
                ],
            ])
            )
            ->add((new LinkGridAction('export'))
            ->setName($this->trans('Export', [], 'Admin.Actions'))
            ->setIcon('cloud_download')
            ->setOptions([
                'route' => 'admin_features_export',
            ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
            ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
            ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
            ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
            ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
            ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
            ->setIcon('storage')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        return (new FilterCollection())
            ->add((new Filter('id_feature_value', NumberType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('id_feature_value')
            )
            ->add((new Filter('value', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search value', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('value')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setAssociatedColumn('actions')
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_feature_values_index',
                'redirect_route_params' => [
                    'featureId' => $this->options['feature_id'],
                ],
            ])
            ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions(): BulkActionCollectionInterface
    {
        // @todo: bulk actions will be added in dedicated PR
        return new BulkActionCollection();
    }
}
