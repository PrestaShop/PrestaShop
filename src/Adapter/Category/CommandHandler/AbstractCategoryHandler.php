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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

/**
 * Encapsulates common behavior for legacy categories
 */
abstract class AbstractCategoryHandler
{
    /**
     * @param array $associatedShopIds
     */
    protected function addShopAssociation(array $associatedShopIds)
    {
        // This is a workaround to make Category's object model work.
        // Inside Category::add() & Category::update() method it checks if shop association is submitted
        // by retrieving data directly from $_POST["checkBoxShopAsso_category"].
        $_POST['checkBoxShopAsso_category'] = $this->getLegacyShopAssociation($associatedShopIds);
    }

    /**
     * Legacy shop association expects both key & value to be the same,
     * because both are used to calculate category position.
     *
     * @param int[] $shopIds
     *
     * @return array
     */
    private function getLegacyShopAssociation(array $shopIds)
    {
        $legacyShopAssociation = [];

        foreach ($shopIds as $shopId) {
            $legacyShopAssociation[$shopId] = $shopId;
        }

        return $legacyShopAssociation;
    }
}
