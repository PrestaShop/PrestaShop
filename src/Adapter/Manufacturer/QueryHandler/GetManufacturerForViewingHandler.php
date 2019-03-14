<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\QueryHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler\GetManufacturerForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use Product;

final class GetManufacturerForViewingHandler implements GetManufacturerForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetManufacturerForViewing $query)
    {
        $manufacturer = $this->getManufacturer($query->getManufacturerId());

        return new ViewableManufacturer(
            [
                'name' => $manufacturer->name,
            ],
            $manufacturer->getAddresses($query->getLanguageId()->getValue()),
            $this->getManufacturerProducts($manufacturer, $query->getLanguageId())
        );
    }

    /**
     * @param ManufacturerId $manufacturerId
     *
     * @return Manufacturer
     */
    private function getManufacturer(ManufacturerId $manufacturerId)
    {
        $manufacturer = new Manufacturer($manufacturerId->getValue());

        if ($manufacturer->id !== $manufacturerId->getValue()) {
            //@todo: throw exception
        }

        return $manufacturer;
    }

    /**
     * @param Manufacturer $manufacturer
     * @param LanguageId $languageId
     *
     * @return array
     */
    private function getManufacturerProducts(Manufacturer $manufacturer, LanguageId $languageId)
    {
        $products = $manufacturer->getProductsLite($languageId->getValue());

        foreach ($products as $i => $product) {
            $products[$i] = new Product($products[$i]['id_product'], false, $languageId->getValue());
            $products[$i]->loadStockData();

            /* Build attributes combinations */
            $combinations = $products[$i]->getAttributeCombinations($languageId->getValue());

            foreach ($combinations as $combination) {
                $combinationsData[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                $combinationsData[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                $combinationsData[$combination['id_product_attribute']]['upc'] = $combination['upc'];
                $combinationsData[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
                $combinationsData[$combination['id_product_attribute']]['attributes'][] = [
                    $combination['group_name'],
                    $combination['attribute_name'],
                    $combination['id_attribute'],
                ];
            }

            if (isset($combinationsData)) {
                foreach ($combinationsData as $key => $productAttribute) {
                    $list = '';
                    foreach ($productAttribute['attributes'] as $attribute) {
                        $list .= $attribute[0] . ' - ' . $attribute[1] . ', ';
                    }
                    $combinationsData[$key]['attributes'] = rtrim($list, ', ');
                }
                isset($combinationsData) ? $products[$i]->combination = $combinationsData : '';
                unset($combinationsData);
            }
        }

        return $products;
    }
}
