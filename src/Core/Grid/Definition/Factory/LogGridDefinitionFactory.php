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
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

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
        $templating = $this->templating;
        $displayEmployee = function ($row) use ($templating) {
            return $templating->render('@AdvancedParameters/LogsPage/Blocks/employee_block.html.twig', [
                'log' => $row,
            ]);
        };

        $columns = new ColumnCollection();
        $columns->add((new Column('id_log', $this->trans('ID', [], 'Admin.Global')))
            ->setFilterFormType(TextType::class)
            ->setPosition(2)
        );
        $columns->add((new Column('id_employee', $this->trans('Employee', [], 'Admin.Global')))
            ->setFilterFormType(TextType::class)
            ->setModifier($displayEmployee)
            ->setRawContent(true)
            ->setPosition(4)
        );
        $columns->add((new Column('severity', $this->trans('Severity (1-4)', [], 'Admin.Advparameters.Feature')))
            ->setFilterFormType(TextType::class)
            ->setPosition(6)
        );
        $columns->add((new Column('message', $this->trans('Message', [], 'Admin.Global')))
            ->setFilterFormType(TextType::class)
            ->setPosition(8)
        );
        $columns->add((new Column('object_type', $this->trans('Object type', [], 'Admin.Advparameters.Feature')))
            ->setFilterFormType(TextType::class)
            ->setPosition(10)
        );
        $columns->add((new Column('object_id', $this->trans('Object ID', [], 'Admin.Advparameters.Feature')))
            ->setFilterFormType(TextType::class)
            ->setPosition(12)
        );
        $columns->add((new Column('error_code', $this->trans('Error code', [], 'Admin.Advparameters.Feature')))
            ->setFilterFormType(TextType::class)
            ->setPosition(14)
        );
        $columns->add((new Column('date_add', $this->trans('Date', [], 'Admin.Global')))
            ->setFilterFormType(TextType::class)
            ->setPosition(16)
        );

        return $columns;
    }
}
