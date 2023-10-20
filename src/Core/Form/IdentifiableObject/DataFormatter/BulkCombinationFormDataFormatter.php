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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

use PrestaShopBundle\Form\Admin\Extension\DisablingSwitchExtension;

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
