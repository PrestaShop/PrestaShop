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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Creates grid definition for Carts grid
 */
final class CartGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'cart';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param HookDispatcherInterface $dispatcher
     * @param ConfigurationInterface $configuration
     */
    public function __construct(HookDispatcherInterface $dispatcher, ConfigurationInterface $configuration)
    {
        parent::__construct($dispatcher);

        $this->configuration = $configuration;
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
        return $this->trans('Shopping Carts', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new BulkActionColumn('carts_bulk'))
                ->setOptions([
                    'bulk_field' => 'id_cart',
                ])
            )
            ->add((new DataColumn('id_cart'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_cart',
                ])
            )
            ->add((new DataColumn('id_order'))
                ->setName($this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'id_order',
                ])
            )
            ->add((new DataColumn('customer_name'))
                ->setName($this->trans('Customer', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'customer_name',
                ])
            )
            ->add((new DataColumn('cart_total'))
                ->setName($this->trans('Total', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'customer_name',
                ])
            )
            ->add((new DataColumn('carrier_name'))
                ->setName($this->trans('Carrier', [], 'Admin.Shipping.Feature'))
                ->setOptions([
                    'field' => 'carrier_name',
                ])
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Date', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
        ;

        if ($this->configuration->get('PS_GUEST_CHECKOUT_ENABLED')) {
            $columns->add((new DataColumn('id_guest'))
                ->setName($this->trans('Online', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_guest',
                ])
            );
        }

        return $columns;
    }
}
