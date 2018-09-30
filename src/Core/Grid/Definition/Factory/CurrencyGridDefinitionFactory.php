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

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class CurrencyGridDefinitionFactory is responsible for defining definition for currency list located in
 * "International -> Localization -> currencies".
 */
final class CurrencyGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var array
     */
    private $statusChoices;

    /**
     * @var string
     */
    private $resetActionUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    /**
     * CurrencyGridDefinitionFactory constructor.
     *
     * @param array $statusChoices
     * @param string $resetActionUrl
     * @param string $redirectionUrl
     */
    public function __construct(
        array $statusChoices,
        $resetActionUrl,
        $redirectionUrl
    ) {
        $this->statusChoices = $statusChoices;
        $this->resetActionUrl = $resetActionUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'Currency';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Currencies', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('conversion_rate'))
                ->setName($this->trans('Exchange rate', [], 'Admin.International.Feature'))
                ->setOptions([
                    'field' => 'conversion_rate',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_currency',
                    'route' => 'admin_currency_toggle_status',
                    'route_param_id' => 'currencyId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_currency_edit',
                                'route_param_name' => 'currencyId',
                                'route_param_field' => 'id_currency',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_currency_delete',
                                'route_param_name' => 'currencyId',
                                'route_param_field' => 'id_currency',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('active', ChoiceType::class)) //todo: change to YesNoChoiceType::class
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $this->statusChoices,
                ])
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'attr' => [
                        'data-url' => $this->resetActionUrl,
                        'data-redirect' => $this->redirectionUrl,
                    ],
                ])
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
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
        ;
    }
}
