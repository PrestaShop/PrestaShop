<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

/**
 * Interface MultiLangConfigurationInterface defines contract multi-language configuration data
 */
interface MultiLangConfigurationInterface
{
    /**
     * Gets configuration from database.
     *
     * @param string $key - a key which represents `name` columns in `configuration` table
     * @param null|int $idShopGroup
     * @param null|int $idShop
     *
     * @return array - returns a value from database. It gets only from active languages. The array key contains
     * language id
     */
    public function get($key, $idShopGroup = null, $idShop = null);

    /**
     * Gets configuration from database.
     *
     * @param string $key - a key which represents `name` columns in `configuration` table
     * @param null|int $idShopGroup
     * @param null|int $idShop
     *
     * @return array - returns a value from database. It gets from installed languages. The array key contains
     * language id
     */
    public function getIncludingInactiveLocales($key, $idShopGroup = null, $idShop = null);
}
