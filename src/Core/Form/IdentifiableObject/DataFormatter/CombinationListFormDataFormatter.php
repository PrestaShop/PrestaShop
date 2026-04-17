<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            '[is_default]' => '[header][is_default]',
        ];

        return $this->formatByPath($formData, $pathAssociations);
    }
}
