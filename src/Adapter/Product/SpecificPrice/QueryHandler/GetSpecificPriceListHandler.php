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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler\GetEditableSpecificPricesListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceListForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Handles @see GetSpecificPriceList using legacy object model
 */
class GetSpecificPriceListHandler implements GetEditableSpecificPricesListHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(
        SpecificPriceRepository $specificPriceRepository
    ) {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSpecificPriceList $query): SpecificPriceListForEditing
    {
        $specificPriceData = $this->specificPriceRepository->getProductSpecificPrices(
            $query->getProductId(),
            $query->getLanguageId(),
            $query->getLimit(),
            $query->getOffset(),
            $query->getFilters()
        );

        return new SpecificPriceListForEditing(
            $this->formatSpecificPricesForListing($specificPriceData),
            $this->specificPriceRepository->getProductSpecificPricesCount(
                $query->getProductId(),
                $query->getLanguageId(),
                $query->getFilters()
            )
        );
    }

    /**
     * @param array $specificPrices
     *
     * @return SpecificPriceForListing[]
     */
    private function formatSpecificPricesForListing(array $specificPrices): array
    {
        return array_map(function (array $specificPrice): SpecificPriceForListing {
            return new SpecificPriceForListing(
                //@todo: missing combination
                (int) $specificPrice['id_specific_price'],
                $specificPrice['reduction_type'],
                new DecimalNumber($specificPrice['reduction']),
                (bool) $specificPrice['reduction_tax'],
                new DecimalNumber($specificPrice['price']),
                (int) $specificPrice['from_quantity'],
                DateTimeUtil::buildNullableDateTime($specificPrice['from']),
                DateTimeUtil::buildNullableDateTime($specificPrice['to']),
                //@todo: not working yet. Need to adjust repository method -> join all lang tables and retrieve names instead of id
                $specificPrice['shop_name'] ?: null,
                $specificPrice['currency_name'] ?: null,
                $specificPrice['country_name'] ?: null,
                $specificPrice['group_name'] ?: null,
                $specificPrice['id_customer'] ?
                    sprintf('%s %s', $specificPrice['customer_firstname'], $specificPrice['customer_lastname']) :
                    null
            );
        }, $specificPrices);
    }
}
