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

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use Currency;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;

/**
 * Decorates original grid data and returns modified prices for grid display as well as color option for stock column
 * when it is out of stock.
 */
class ProductLightGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $productGridDataFactory;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @var bool
     */
    private $stockManagementEnabled;

    /**
     * @param GridDataFactoryInterface $productGridDataFactory
     * @param Repository $localeRepository
     * @param string $contextLocale
     * @param int $defaultCurrencyId
     */
    public function __construct(
        GridDataFactoryInterface $productGridDataFactory,
        Repository $localeRepository,
        string $contextLocale,
        int $defaultCurrencyId,
        bool $stockManagementEnabled
    ) {
        $this->productGridDataFactory = $productGridDataFactory;
        $this->locale = $localeRepository->getLocale(
            $contextLocale
        );
        $this->defaultCurrencyId = $defaultCurrencyId;
        $this->stockManagementEnabled = $stockManagementEnabled;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return GridData
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $productsData = $this->productGridDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification($productsData->getRecords()->all());

        return new GridData(
            new RecordCollection($modifiedRecords),
            $productsData->getRecordsTotal(),
            $productsData->getQuery()
        );
    }

    /**
     * Applies modifications for product grid.
     *
     * @param array $products
     *
     * @return array
     */
    private function applyModification(array $products): array
    {
        $currency = new Currency($this->defaultCurrencyId);
        foreach ($products as $i => $product) {
            $products[$i]['price_tax_excluded'] = $this->locale->formatPrice(
                $products[$i]['price_tax_excluded'],
                $currency->iso_code
            );

            if ($this->stockManagementEnabled && $products[$i]['quantity'] <= 0) {
                $products[$i]['quantity_color'] = 'danger';
            }
        }

        return $products;
    }
}
