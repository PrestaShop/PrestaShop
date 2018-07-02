<?php
/**
 * 2007-2018 PrestaShop
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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\SimpleColumn;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class RequestSqlGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('SQL Manager', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_value' => 'id_request_sql',
                ])
            )
            ->add((new SimpleColumn('id_request_sql'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
            )
            ->add((new SimpleColumn('name'))
                ->setName($this->trans('SQL query Name', [], 'Admin.Advparameters.Feature'))
            )
            ->add((new SimpleColumn('sql'))
                ->setName($this->trans('SQL query', [], 'Admin.Advparameters.Feature'))
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Global.Actions'))
                ->setOptions([
                    'filter_type' => SubmitType::class,
                    'filter_type_options' => [
                        'label' => $this->trans('Search', [], 'Global.Actions'),
                        'attr' => [
                            'class' => 'btn btn-primary',
                        ],
                    ],
                ])
            )
        ;
    }
}
