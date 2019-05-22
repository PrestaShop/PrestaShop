<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines grid for credit slip listing
 */
final class CreditSlipGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'credit_slip';

    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(HookDispatcherInterface $hookDispatcher, $dateFormat)
    {
        parent::__construct($hookDispatcher);
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Credit slips', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_credit_slip'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_order_slip',
                ])
            )
            ->add((new DataColumn('id_order'))
                ->setName($this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'id_order',
                ])
            )
            ->add((new DateTimeColumn('date_issued'))
                ->setName($this->trans('Date issued', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                    'format' => $this->dateFormat,
                ])
            )
            ->add(
                (new LinkColumn('pdf'))
                    ->setName($this->trans('PDF', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'link_value',
                        'sortable' => false,
                        'route' => 'admin_credit_slips_pdf',
                        'route_param_name' => 'creditSlipId',
                        'route_param_field' => 'id_order_slip',
                    ])
            )
            ->add((new ActionColumn('actions')));
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_credit_slip', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_credit_slip')
            )
            ->add((new Filter('id_order', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search order ID', [], 'Admin.Orderscustomers.Feature'),
                    ],
                ])
                ->setAssociatedColumn('id_order')
            )
            ->add((new Filter('date_issued', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('date_issued')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'admin_credit_slips_index',
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }
}
