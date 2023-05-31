<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderDetailCustomizations;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderDetailCustomizationsHandlerInterface;
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
     * @var int
     */
    private $contextLangId;

    /**
     * OrderReturnProductsGridDataFactory constructor.
     *
     * @param GridDataFactoryInterface $orderReturnProductsGridDataFactory
     * @param GetOrderDetailCustomizationsHandlerInterface $getOrderDetailCustomizationHandler
     * @param int $contextLangId
     */
    public function __construct(
        GridDataFactoryInterface $orderReturnProductsGridDataFactory,
        GetOrderDetailCustomizationsHandlerInterface $getOrderDetailCustomizationHandler,
        int $contextLangId
    ) {
        $this->orderReturnProductsGridDataFactory = $orderReturnProductsGridDataFactory;
        $this->getOrderDetailCustomizationHandler = $getOrderDetailCustomizationHandler;
        $this->contextLangId = $contextLangId;
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
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException
     */
    private function applyModification(array $orderReturnProducts): array
    {
        $modifiedOrderReturnProducts = [];
        foreach ($orderReturnProducts as $orderReturnProduct) {
            $getOrderDetailCustomization = new GetOrderDetailCustomizations(
                (int) $orderReturnProduct['id_order_detail'],
                $this->contextLangId
            );
            $orderReturnProduct['customizations'] = $this->getOrderDetailCustomizationHandler->handle($getOrderDetailCustomization);
            $modifiedOrderReturnProducts[] = $orderReturnProduct;
        }

        return $modifiedOrderReturnProducts;
    }
}
