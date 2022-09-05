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

namespace PrestaShopBundle\Bridge\Helper\Listing\HelperBridge;

use Db;
use DbQuery;
use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;

/**
 * This class customize the result of the list for the feature controller.
 */
class FeatureHelperListBridge extends HelperListBridge
{
    public function generateListQuery(
        HelperListConfiguration $helperListConfiguration,
        int $idLang
    ): void {
        parent::generateListQuery($helperListConfiguration, $idLang);

        $nbItems = count($helperListConfiguration->list);
        for ($i = 0; $i < $nbItems; ++$i) {
            $item = &$helperListConfiguration->list[$i];

            $query = new DbQuery();
            $query->select('COUNT(fv.id_feature_value) as count_values');
            $query->from('feature_value', 'fv');
            $query->where('fv.id_feature =' . (int) $item['id_feature']);
            $query->where('(fv.custom=0 OR fv.custom IS NULL)');
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            $item['value'] = (int) $res;
            unset($query);
        }
    }
}
