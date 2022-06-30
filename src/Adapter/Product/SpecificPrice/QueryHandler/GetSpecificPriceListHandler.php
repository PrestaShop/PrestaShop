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
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler\GetSpecificPriceListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Handles @see GetSpecificPriceList using legacy object model
 */
class GetSpecificPriceListHandler implements GetSpecificPriceListHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     * @param AttributeRepository $attributeRepository
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     */
    public function __construct(
        SpecificPriceRepository $specificPriceRepository,
        AttributeRepository $attributeRepository,
        CombinationNameBuilderInterface $combinationNameBuilder
    ) {
        $this->specificPriceRepository = $specificPriceRepository;
        $this->attributeRepository = $attributeRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSpecificPriceList $query): SpecificPriceList
    {
        $specificPriceData = $this->specificPriceRepository->getProductSpecificPrices(
            $query->getProductId(),
            $query->getLanguageId(),
            $query->getLimit(),
            $query->getOffset(),
            $query->getFilters()
        );

        return new SpecificPriceList(
            $this->formatSpecificPricesForListing($specificPriceData, $query->getLanguageId()),
            $this->specificPriceRepository->countProductSpecificPrices(
                $query->getProductId(),
                $query->getLanguageId(),
                $query->getFilters()
            )
        );
    }

    /**
     * @param array<int, array<string, string|null>> $specificPrices
     * @param LanguageId $languageId
     *
     * @return SpecificPriceForListing[]
     */
    private function formatSpecificPricesForListing(array $specificPrices, LanguageId $languageId): array
    {
        $attributesInfo = $this->getAttributesInfo($specificPrices, $languageId);

        $specificPricesForListing = [];
        foreach ($specificPrices as $specificPrice) {
            $combinationId = (int) $specificPrice['id_product_attribute'];
            if ($combinationId && !isset($attributesInfo[$combinationId])) {
                throw new CombinationException(sprintf('Failed to fetch combination "%d" info.', $combinationId));
            }

            // VO stores percent expressed based on 100, while the DB stored the float value (VO: 57.5 - DB: 0.575)
            $reductionValue = new DecimalNumber($specificPrice['reduction']);
            if ($specificPrice['reduction_type'] === Reduction::TYPE_PERCENTAGE) {
                $reductionValue = $reductionValue->times(new DecimalNumber('100'));
            }

            $specificPricesForListing[] = new SpecificPriceForListing(
                (int) $specificPrice['id_specific_price'],
                $specificPrice['reduction_type'],
                $reductionValue,
                (bool) $specificPrice['reduction_tax'],
                InitialPrice::isInitialPriceValue($specificPrice['price']) ? new InitialPrice() : new FixedPrice($specificPrice['price']),
                (int) $specificPrice['from_quantity'],
                DateTimeUtil::buildNullableDateTime($specificPrice['from']),
                DateTimeUtil::buildNullableDateTime($specificPrice['to']),
                $combinationId ? $this->combinationNameBuilder->buildName($attributesInfo[$combinationId]) : null,
                $specificPrice['shop_name'],
                $specificPrice['currency_name'],
                $specificPrice['currency_iso_code'],
                $specificPrice['country_name'],
                $specificPrice['group_name'],
                $this->buildCustomerFullName($specificPrice)
            );
        }

        return $specificPricesForListing;
    }

    /**
     * @param array<string, string|null> $specificPrice
     *
     * @return string|null
     */
    private function buildCustomerFullName(array $specificPrice): ?string
    {
        $customerName = null;
        if ((int) $specificPrice['id_customer']) {
            $customerName = sprintf('%s %s', $specificPrice['customer_firstname'], $specificPrice['customer_lastname']);
        }

        return $customerName;
    }

    /**
     * @param array<int, array<string, string|null>> $specificPrices
     * @param LanguageId $languageId
     *
     * @return array<int, CombinationAttributeInformation[]>
     */
    private function getAttributesInfo(array $specificPrices, LanguageId $languageId): array
    {
        $combinationIds = [];
        foreach ($specificPrices as $specificPrice) {
            if ($specificPrice['id_product_attribute']) {
                $combinationIds[] = new CombinationId((int) $specificPrice['id_product_attribute']);
            }
        }

        return $this->attributeRepository->getAttributesInfoByCombinationIds($combinationIds, $languageId);
    }
}
