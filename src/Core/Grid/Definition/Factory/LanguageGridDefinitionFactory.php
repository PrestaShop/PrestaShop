<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class LanguageGridDefinitionFactory creates definition for languages grid.
 */
final class LanguageGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $resetUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @param string $resetUrl
     * @param string $redirectUrl
     */
    public function __construct($resetUrl, $redirectUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'language';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Languages', array(), 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('language_bulk'))
                ->setOptions(array(
                    'bulk_field' => 'id_lang',
                ))
            )
            ->add(
                (new DataColumn('id_lang'))
                ->setName($this->trans('ID', array(), 'Admin.Global'))
                ->setOptions(array(
                    'field' => 'id_lang',
                ))
            )
            ->add(
                (new ImageColumn('flag'))
                ->setName($this->trans('Flag', array(), 'Admin.International.Feature'))
                ->setOptions(array(
                    'src_field' => 'flag',
                ))
            )
            ->add(
                (new LinkColumn('name'))
                ->setName($this->trans('Name', array(), 'Admin.Global'))
                ->setOptions(array(
                    'field' => 'name',
                    'route' => 'admin_languages_edit',
                    'route_param_name' => 'languageId',
                    'route_param_field' => 'id_lang',
                ))
            )
            ->add(
                (new DataColumn('iso_code'))
                ->setName($this->trans('ISO code', array(), 'Admin.International.Feature'))
                ->setOptions(array(
                    'field' => 'iso_code',
                ))
            )
            ->add(
                (new DataColumn('language_code'))
                ->setName($this->trans('Language code', array(), 'Admin.International.Feature'))
                ->setOptions(array(
                    'field' => 'language_code',
                ))
            )
            ->add(
                (new DataColumn('date_format_lite'))
                ->setName($this->trans('Date format', array(), 'Admin.International.Feature'))
                ->setOptions(array(
                    'field' => 'date_format_lite',
                ))
            )
            ->add(
                (new DataColumn('date_format_full'))
                ->setName($this->trans('Date format (full)', array(), 'Admin.International.Feature'))
                ->setOptions(array(
                    'field' => 'date_format_full',
                ))
            )
            ->add(
                (new ToggleColumn('active'))
                ->setName($this->trans('Enabled', array(), 'Admin.Global'))
                ->setOptions(array(
                    'field' => 'active',
                    'primary_field' => 'id_lang',
                    'route' => 'admin_languages_index',
                    'route_param_name' => 'languageId',
                ))
            )
            ->add(
                (new ActionColumn('actions'))
                ->setName($this->trans('Actions', array(), 'Admin.Global'))
                ->setOptions(array(
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', array(), 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions(array(
                                'route' => 'admin_languages_edit',
                                'route_param_name' => 'languageId',
                                'route_param_field' => 'id_lang',
                            ))
                        )
                        ->add(
                            (new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', array(), 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions(array(
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    array(),
                                    'Admin.Notifications.Warning'
                                ),
                                'route' => 'admin_languages_index',
                                'route_param_name' => 'languageId',
                                'route_param_field' => 'id_lang',
                            ))
                        ),
                ))
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
             ->add(
                 (new Filter('id_lang', NumberType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                        'placeholder' => $this->translator->trans('Search ID', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('id_lang')
             )
             ->add(
                 (new Filter('name', TextType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                         'placeholder' => $this->translator->trans('Search name', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('name')
             )
             ->add(
                 (new Filter('iso_code', TextType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                         'placeholder' => $this->translator->trans('Search iso code', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('iso_code')
             )
             ->add(
                 (new Filter('language_code', TextType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                         'placeholder' => $this->translator->trans('Search code', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('language_code')
             )
             ->add(
                 (new Filter('date_format_lite', TextType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                         'placeholder' => $this->translator->trans('Search date format', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('date_format_lite')
             )
             ->add(
                 (new Filter('date_format_full', TextType::class))
                 ->setTypeOptions(array(
                     'required' => false,
                     'attr' => array(
                         'placeholder' => $this->translator->trans('Search date format', array(), 'Admin.International.Help'),
                     ),
                 ))
                 ->setAssociatedColumn('date_format_full')
             )
             ->add(
                 (new Filter('active', ChoiceType::class))
                 ->setTypeOptions(array(
                     'choices' => array(
                         $this->trans('Yes', array(), 'Admin.Global') => 1,
                         $this->trans('No', array(), 'Admin.Global') => 0,
                     ),
                     'required' => false,
                     'choice_translation_domain' => false,
                 ))
                 ->setAssociatedColumn('active')
             )
             ->add(
                 (new Filter('actions', SearchAndResetType::class))
                 ->setTypeOptions(array(
                     'attr' => array(
                         'data-url' => $this->resetUrl,
                         'data-redirect' => $this->redirectUrl,
                     ),
                 ))
                 ->setAssociatedColumn('actions')
             )
         ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', array(), 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', array(), 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', array(), 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                ->setName($this->trans('Enable selection', array(), 'Admin.Actions'))
                ->setOptions(array(
                    'submit_route' => 'admin_languages_index',
                ))
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                ->setName($this->trans('Disable selection', array(), 'Admin.Actions'))
                ->setOptions(array(
                    'submit_route' => 'admin_languages_index',
                ))
            )
            ->add(
                (new SubmitBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', array(), 'Admin.Actions'))
                ->setOptions(array(
                    'submit_route' => 'admin_languages_index',
                    'confirm_message' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                ))
            )
        ;
    }
}
