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

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchParametersFactoryInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GridFactory is responsible for creating grid from it's definition
 */
final class GridFactory implements GridFactoryInterface
{
    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    /**
     * @var SearchParametersFactoryInterface
     */
    private $searchParametersFactory;

    /**
     * @param HookDispatcher $dispatcher
     * @param SearchParametersFactoryInterface $searchParametersFactory
     */
    public function __construct(
        HookDispatcher $dispatcher,
        SearchParametersFactoryInterface $searchParametersFactory
    ) {
        $this->dispatcher = $dispatcher;
        $this->searchParametersFactory = $searchParametersFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataProviderInterface $dataProvider,
        Request $request
    ) {
        $gridDefinition = $definitionFactory->create();

        $this->dispatcher->dispatchForParameters('modifyGridDefinition', [
            'grid_definition' => $gridDefinition,
        ]);

        $searchParameters = $this->searchParametersFactory->createFromRequest($request, $gridDefinition);

        $rows = $dataProvider->getRows($searchParameters);
        $rowsTotal = $dataProvider->getRowsTotal();

        $data = new GridData($rows, $rowsTotal);
        $grid = new Grid(
            $gridDefinition,
            $data,
            $searchParameters
        );

        return $grid;
    }
}
