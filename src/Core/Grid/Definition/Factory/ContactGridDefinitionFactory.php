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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ContactGridDefinitionFactory is responsible for creating Contact grid definition.
 */
final class ContactGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    /**
     * @var string
     */
    private $resetSearchUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetSearchUrl
     * @param string $redirectionUrl
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $resetSearchUrl,
        $redirectionUrl
    ) {
        parent::__construct($hookDispatcher);
        $this->resetSearchUrl = $resetSearchUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Contacts', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_contact',
                    ])
            )
            ->add(
                (new DataColumn('id_contact'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_contact',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Title', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('email'))
                    ->setName($this->trans('Email address', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'email',
                    ])
            )
            ->add(
                (new DataColumn('description'))
                    ->setName($this->trans('Description', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'description',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setIcon('edit')
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'route' => 'admin_contacts_edit',
                                        'route_param_name' => 'contactId',
                                        'route_param_field' => 'id_contact',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_contacts_delete',
                                    'contactId',
                                    'id_contact'
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_contact', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_contact')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('description', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('description')
            )

            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'data-url' => $this->resetSearchUrl,
                            'data-redirect' => $this->redirectionUrl,
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_contacts_delete_bulk')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }
}
