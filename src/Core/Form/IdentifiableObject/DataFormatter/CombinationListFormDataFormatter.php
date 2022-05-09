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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

/**
 * This class transforms the data from list form into data adapted to the combination form structure,
 * since the forms are not constructed the same way the goal is to rebuild the same data values with the
 * right property path. When the data is not detected it is not included in the formatted data.
 */
class CombinationListFormDataFormatter extends AbstractFormDataFormatter
{
    /**
     * @param array<string, mixed> $formData
     *
     * @return array<string, mixed>
     */
    public function format(array $formData): array
    {
        $pathAssociations = [
            '[reference]' => '[references][reference]',
            '[impact_on_price_te]' => '[price_impact][price_tax_excluded]',
            '[impact_on_price_ti]' => '[price_impact][price_tax_included]',
            '[delta_quantity][delta]' => '[stock][quantities][delta_quantity][delta]',
            '[is_default]' => '[is_default]',
        ];

        return $this->formatByPath($formData, $pathAssociations);
    }
}
