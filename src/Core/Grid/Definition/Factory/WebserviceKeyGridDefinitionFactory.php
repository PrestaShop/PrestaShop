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

use PrestaShop\PrestaShop\Core\Grid\Action\BulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\EnabledOrNotColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee\EmployeeNameWithAvatarColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\SimpleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Status\SeverityLevelColumn;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class WebserviceKeyGridDefinitionFactory is responsible for creating new instance of Webservice key grid definition
 */
final class WebserviceKeyGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'webservice_keys';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Webservice keys', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_value' => 'key',
                ])
            )
            ->add((new SimpleColumn('key'))
                ->setName($this->trans('Key', [], 'Global.Actions'))
            )
            ->add((new SimpleColumn('description'))
                ->setName($this->trans('Key description', [], 'Global.Actions'))
            )
            ->add((new EnabledOrNotColumn('active'))
                ->setName($this->trans('Enabled', [], 'Global.Actions'))
                ->setOptions([
                    'sortable' => false,
                ])
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

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return null;
    }

    protected function getBulkActions()
    {
        return null;
    }
}
