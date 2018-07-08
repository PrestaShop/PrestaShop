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

namespace PrestaShopBundle\Twig\Extension;

use Error;
use Symfony\Component\Templating\EngineInterface;
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction as SimpleFunction;

/**
 * Class ColumnExtension is responsible for providing twig functions:
 *
 * 1. column_content(column, row, grid) - renders column content based on column type
 * 2. column_header(column, grid) - renders column header based on column type
 * 3. column_flter(column, grid) - renders column filter based on column type
 */
class ColumnExtension extends AbstractExtension
{
    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function getFunctions()
    {
        return [
            new SimpleFunction('column_content', [$this, 'renderColumnContent'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function renderColumnContent(array $column, array $row, array $grid)
    {
        $columnsPath = '@PrestaShop/Admin/Common/Grid/Columns';
        $columnType = $column['type'];
        $gridId = $grid['id'];

        $gridSpecificColumnTemplatePath = sprintf('%s/%s_%s.html.twig', $columnsPath, $gridId, $columnType);
        if ($this->templating->exists($gridSpecificColumnTemplatePath)) {
            return $this->templating->render($gridSpecificColumnTemplatePath, [
                'column' => $column,
                'row' => $row,
                'grid' => $grid,
            ]);
        }

        $columnTemplatePath = sprintf('%s/%s.html.twig', $columnsPath, $columnType);
        if ($this->templating->exists($columnTemplatePath)) {
            return $this->templating->render($columnTemplatePath, [
                'column' => $column,
                'row' => $row,
                'grid' => $grid,
            ]);
        }

        throw new Error(sprintf('Content template for column type "%s" was not found.', $columnType));
    }
}
