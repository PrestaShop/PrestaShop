<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Import\Configuration;

use Db;
use DbQuery;

/**
 * Class ImportDataMatchProvider is responsible for getting import data match configuration which are used to load
 * different kind of configurations for import list in Advanced parameters -> Import -> step 2
 */
class ImportDataMatchProvider
{
    /**
     * Gets import match configuration which contains id and name of the configuration
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getImportMatchConfiguration()
    {
        $query = new DbQuery();
        $query->select('`id_import_match`, `name`');
        $query->from('import_match');
        $result = Db::getInstance()->executeS($query);

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }
}
