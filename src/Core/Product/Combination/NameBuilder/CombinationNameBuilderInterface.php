<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;

interface CombinationNameBuilderInterface
{
    /**
     * Build combination name from related attributes and attribute group names
     *
     * @param CombinationAttributeInformation[] $attributesInfo
     *
     * @return string
     */
    public function buildName(array $attributesInfo): string;

    /**
     * Build combination full name from related product and attributes and attribute group names
     *
     * @param string $productName
     * @param CombinationAttributeInformation[] $attributesInfo
     *
     * @return string
     */
    public function buildFullName(string $productName, array $attributesInfo): string;
}
