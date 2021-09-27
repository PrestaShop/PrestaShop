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

namespace PrestaShop\PrestaShop\Core\Product\Combination;

/**
 * Builds combination name by attributes information
 */
class CombinationNameBuilder implements CombinationNameBuilderInterface
{
    /**
     * {@inheritdoc}
     *
     * @todo: could be reused in src/Adapter/Product/Combination/QueryHandler/GetCombinationForEditingHandler.php
     *   but is it really the same for SpecificPriceForListing? old page seems to only show attribute names there (not showing attribute_groups)
     */
    public function buildName(array $attributesInfo): string
    {
        //@todo: could also be configurable (Configuration::get('PS_COMBINATION_NAME_DELIMITER')
        return implode(', ', array_map(function ($attribute) {
            return sprintf(
                //@todo: format could be configurable  (Configuration::get('PS_COMBINATION_NAME_FORMAT')
                '%s - %s',
                $attribute['attribute_group_name'],
                $attribute['attribute_name']
            );
        }, $attributesInfo));
    }
}
