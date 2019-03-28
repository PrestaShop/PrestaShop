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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler\GetManufacturerForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use Product;

/**
 * Handles getting manufacturer for viewing query using legacy object model
 */
final class GetManufacturerForViewingHandler implements GetManufacturerForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetManufacturerForViewing $query)
    {
        $manufacturer = $this->getManufacturer($query->getManufacturerId());

        return new ViewableManufacturer(
            $manufacturer->name,
            $this->getManufacturerAddresses($manufacturer, $query->getLanguageId()),
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
            throw new ManufacturerNotFoundException(
                sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue())
            );
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
        $products = [];
        $manufacturerProducts = $manufacturer->getProductsLite($languageId->getValue());

        foreach ($manufacturerProducts as $productData) {
            $product = new Product($productData['id_product'], false, $languageId->getValue());
            $product->loadStockData();

            $productCombinations = $product->getAttributeCombinations($languageId->getValue());
            $combinations = [];

            foreach ($productCombinations as $combination) {
                $attributeId = $combination['id_product_attribute'];

                if (!isset($combinations[$attributeId])) {
                    $combinations[$attributeId] = [
                        'reference' => $combination['reference'],
                        'ean13' => $combination['ean13'],
                        'upc' => $combination['upc'],
                        'quantity' => $combination['quantity'],
                        'attributes' => '',
                    ];
                }

                $attribute = sprintf(
                    '%s - %s',
                    $combination['group_name'],
                    $combination['attribute_name']
                );

                if (!empty($combinations[$attributeId]['attributes'])) {
                    $attribute = sprintf(', %s', $attribute);
                }

                $combinations[$attributeId]['attributes'] .= $attribute;
            }

            $products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'reference' => $product->reference,
                'ean13' => $product->ean13,
                'upc' => $product->upc,
                'quantity' => $product->quantity,
                'combinations' => $combinations,
            ];
        }

        return $products;
    }

    /**
     * @param Manufacturer $manufacturer
     * @param LanguageId $languageId
     *
     * @return array
     */
    private function getManufacturerAddresses(Manufacturer $manufacturer, LanguageId $languageId)
    {
        $addresses = [];
        $manufacturerAddresses = $manufacturer->getAddresses($languageId->getValue());

        foreach ($manufacturerAddresses as $address) {
            $addresses[] = [
                'id' => $address['id_address'],
                'first_name' => $address['firstname'],
                'last_name' => $address['lastname'],
                'address1' => $address['address1'],
                'address2' => $address['address2'],
                'postcode' => $address['postcode'],
                'city' => $address['city'],
                'state' => $address['state'],
                'country' => $address['country'],
                'phone' => $address['phone'],
                'phone_mobile' => $address['phone_mobile'],
                'other' => $address['other'],
            ];
        }

        return $addresses;
    }
}
