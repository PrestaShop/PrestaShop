<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;

/**
 * This class transforms the data from bulk form into data adapted to the combination form structure,
 * since the forms are not constructed the same way the goal is to rebuild the same data values with the
 * right property path. When the data is not detected it is not included in the formatted data.
 */
class BulkCombinationFormDataFormatter extends AbstractFormDataFormatter
{
    /**
     * @param array<string, mixed> $formData
     *
     * @return array<string, mixed>
     */
    public function format(array $formData): array
    {
        $pathAssociations = [
            '[references][reference]' => '[references][reference]',
            '[references][mpn]' => '[references][mpn]',
            '[references][upc]' => '[references][upc]',
            '[references][ean_13]' => '[references][ean_13]',
            '[references][isbn]' => '[references][isbn]',
            '[price][price_tax_included]' => '[price_impact][price_tax_included]',
            '[price][wholesale_price]' => '[price_impact][wholesale_price]',
            '[price][price_tax_excluded]' => '[price_impact][price_tax_excluded]',
            '[price][unit_price]' => '[price_impact][unit_price]',
            '[price][weight]' => '[price_impact][weight]',
            '[stock][delta_quantity][delta]' => '[stock][quantities][delta_quantity][delta]',
            '[stock][fixed_quantity]' => '[stock][quantities][fixed_quantity]',
            '[stock][minimal_quantity]' => '[stock][quantities][minimal_quantity]',
            '[stock][stock_location]' => '[stock][options][stock_location]',
            '[stock][low_stock_threshold][threshold_value]' => '[stock][options][low_stock_threshold]',
            '[stock][low_stock_threshold][low_stock_alert]' => sprintf(
                '[stock][options][%slow_stock_threshold]',
                DisablingSwitchExtension::FIELD_PREFIX
            ),
            '[stock][available_date]' => '[stock][available_date]',
            '[stock][available_now_label]' => '[stock][available_now_label]',
            '[stock][available_later_label]' => '[stock][available_later_label]',
        ];

        $formattedData = $this->formatByPath($formData, $pathAssociations);

        // We only update images if disabling_switch_images value is truthy
        if (!empty($formData['images'][sprintf('%simages', DisablingSwitchExtension::FIELD_PREFIX)])) {
            if (empty($formData['images']['images'])) {
                // Images are collection of checkboxes and there are no values submitted if none of them are checked, but
                // truthy disabling_switch_images value suggests, that it was intended to "unselect" all images
                // so we adapt array structure accordingly
                $formattedData['images'] = [];
            } else {
                // if images array is not empty, we simply adapt array structure to fit combinationForm structure
                $formattedData['images'] = $formData['images']['images'];
            }
        }

        return $formattedData;
    }
}
