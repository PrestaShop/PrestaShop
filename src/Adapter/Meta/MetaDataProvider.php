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

namespace PrestaShop\PrestaShop\Adapter\Meta;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Meta\MetaDataProviderInterface;

/**
 * Class MetaDataProvider is responsible for providing data related with meta entity.
 */
class MetaDataProvider implements MetaDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdByPage($pageName)
    {
        $query = new DbQuery();
        $query->select('`id_meta`');
        $query->from('meta');
        $query->where('`page`= "' . pSQL($pageName) . '"');

        $idMeta = 0;
        $result = Db::getInstance()->getValue($query);

        if ($result) {
            $idMeta = $result;
        }

        return $idMeta;
    }
}
