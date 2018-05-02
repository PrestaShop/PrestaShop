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

use PrestaShop\PrestaShop\Core\Grid\Column\Column;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getIdentifier()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Logs', [], 'Admin.Advparameters.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return [
            (new Column('id_log', $this->trans('ID', [], 'Admin.Global'), TextType::class))
                ->setPosition(2),
            (new Column('id_employee', $this->trans('Employee', [], 'Admin.Global'), TextType::class))
                ->setPosition(4),
            (new Column('severity', $this->trans('Severity (1-4)', [], 'Admin.Advparameters.Feature'), TextType::class))
                ->setPosition(6),
            (new Column('message', $this->trans('Message', [], 'Admin.Global'), TextType::class))
                ->setPosition(8),
            (new Column('object_type', $this->trans('Object type', [], 'Admin.Advparameters.Feature'), TextType::class))
                ->setPosition(10),
            (new Column('object_id', $this->trans('Object ID', [], 'Admin.Advparameters.Feature'), TextType::class))
                ->setPosition(12),
            (new Column('error_code', $this->trans('Error code', [], 'Admin.Advparameters.Feature'), TextType::class))
                ->setPosition(14),
            (new Column('date_add', $this->trans('Date', [], 'Admin.Global'), TextType::class))
                ->setPosition(16),
        ];
    }
}
