<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderDetailCustomizations;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderDetailCustomizationsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Gets data for order return products grid
 */
class OrderReturnProductsGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $orderReturnProductsGridDataFactory;

    /**
     * @var GetOrderDetailCustomizationsHandlerInterface
     */
    private $getOrderDetailCustomizationHandler;

    /**
     * OrderReturnProductsGridDataFactory constructor.
     *
     * @param GridDataFactoryInterface $orderReturnProductsGridDataFactory
     * @param GetOrderDetailCustomizationsHandlerInterface $getOrderDetailCustomizationHandler
     */
    public function __construct(
        GridDataFactoryInterface $orderReturnProductsGridDataFactory,
        GetOrderDetailCustomizationsHandlerInterface $getOrderDetailCustomizationHandler
    ) {
        $this->orderReturnProductsGridDataFactory = $orderReturnProductsGridDataFactory;
        $this->getOrderDetailCustomizationHandler = $getOrderDetailCustomizationHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $orderReturnProducts = $this->orderReturnProductsGridDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $orderReturnProducts->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $orderReturnProducts->getRecordsTotal(),
            $orderReturnProducts->getQuery()
        );
    }

    /**
     * @param array $orderReturnProducts
     *
     * @return array
     */
    private function applyModification(array $orderReturnProducts): array
    {
        $modifiedOrderReturnProducts = [];
        foreach ($orderReturnProducts as $orderReturnProduct) {
            $getOrderDetailCustomization = new GetOrderDetailCustomizations(
                new OrderReturnDetailId((int) $orderReturnProduct['id_order_detail'])
        );
            $orderReturnProduct['customizations'] = $this->getOrderDetailCustomizationHandler->handle($getOrderDetailCustomization);
            $modifiedOrderReturnProducts[] = $orderReturnProduct;
        }

        return $modifiedOrderReturnProducts;
    }
}
