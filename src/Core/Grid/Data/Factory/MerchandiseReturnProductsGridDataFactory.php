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

use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetOrderDetailCustomization;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryHandler\GetOrderDetailCustomizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Gets data for merchandise return products grid
 */
class MerchandiseReturnProductsGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $merchandiseReturnProductsGridDataFactory;

    /**
     * @var GetOrderDetailCustomizationHandlerInterface
     */
    private $getOrderDetailCustomizationHandler;

    /**
     * MerchandiseReturnProductsGridDataFactory constructor.
     *
     * @param GridDataFactoryInterface $merchandiseReturnProductsGridDataFactory
     * @param GetOrderDetailCustomizationHandlerInterface $getOrderDetailCustomizationHandler
     */
    public function __construct(
        GridDataFactoryInterface $merchandiseReturnProductsGridDataFactory,
        GetOrderDetailCustomizationHandlerInterface $getOrderDetailCustomizationHandler
    ) {
        $this->merchandiseReturnProductsGridDataFactory = $merchandiseReturnProductsGridDataFactory;
        $this->getOrderDetailCustomizationHandler = $getOrderDetailCustomizationHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $merchandiseReturnProducts = $this->merchandiseReturnProductsGridDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $merchandiseReturnProducts->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $merchandiseReturnProducts->getRecordsTotal(),
            $merchandiseReturnProducts->getQuery()
        );
    }

    /**
     * @param array $merchandiseReturnProducts
     *
     * @return array
     */
    private function applyModification(array $merchandiseReturnProducts): array
    {
        $modifiedMerchandiseReturnProducts = [];
        foreach ($merchandiseReturnProducts as $merchandiseReturnProduct) {
            $getOrderDetailCustomization = new GetOrderDetailCustomization((int) $merchandiseReturnProduct['id_order_detail']);
            $merchandiseReturnProduct['customizations'] = $this->getOrderDetailCustomizationHandler->handle($getOrderDetailCustomization);
            $modifiedMerchandiseReturnProducts[] = $merchandiseReturnProduct;
        }

        return $modifiedMerchandiseReturnProducts;
    }
}
