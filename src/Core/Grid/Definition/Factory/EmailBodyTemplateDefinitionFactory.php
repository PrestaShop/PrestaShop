<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class EmailBodyTemplateDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'email_body_template';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Email body templates', [], 'Admin.Navigation.Menu');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('template_name'))
                    ->setName($this->trans('Template name', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'template_name',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new DataColumn('source'))
                    ->setName($this->trans('Source', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'source',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new DataColumn('module_name'))
                    ->setName($this->trans('Module', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'module_name',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new DataColumn('has_html'))
                    ->setName($this->trans('HTML', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'has_html_label',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('has_txt'))
                    ->setName($this->trans('TXT', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'has_txt_label',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_email_body_translation_edit',
                                        'route_param_name' => 'templateName',
                                        'route_param_field' => 'template_name',
                                        'extra_route_params' => [
                                            'source' => 'source_with_module',
                                            'locale' => 'locale',
                                        ],
                                        'clickable_row' => true,
                                    ])
                            ),
                    ])
            );
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('template_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search template name', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('template_name')
            )
            ->add(
                (new Filter('source', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'placeholder' => $this->trans('All', [], 'Admin.Global'),
                        'choices' => [
                            $this->trans('Core', [], 'Admin.International.Feature') => 'core',
                            $this->trans('Module', [], 'Admin.Global') => 'module',
                        ],
                    ])
                    ->setAssociatedColumn('source')
            )
            ->add(
                (new Filter('module_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search module', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('module_name')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_email_body_translation_index',
                        'redirect_route_params' => [
                            'locale' => 'en',
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }
}
