<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency;

use PrestaShopBundle\Currency\Repository\Cache;
use PrestaShopBundle\Currency\Repository\CLDR;
use PrestaShopBundle\Currency\Repository\Database;

class ManagerFactory
{
    /**
     * Get a currency manager instance
     *
     * The currency manager wil be configured with cache and database as installed currencies repository data sources,
     * and CLDR as reference source
     *
     * @param string $localeCode The locale in which currency data should be retrieved
     *
     * @return Manager
     */
    public function build($localeCode)
    {
        $cache    = new Cache($localeCode);
        $database = new Database($localeCode);
        $cldr     = new CLDR($localeCode);

        $installedCurrencyRepository = new Repository([$cache, $database]);
        $referenceRepository         = new Repository([$cldr]);

        return new Manager($installedCurrencyRepository, $referenceRepository);
    }
}
