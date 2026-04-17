<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\QueryHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
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
            throw new ManufacturerNotFoundException(sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue()));
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
                        'mpn' => $combination['mpn'],
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
                'mpn' => $product->mpn,
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
