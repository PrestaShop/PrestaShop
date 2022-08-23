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
use Symfony\Component\HttpFoundation\Request;

/**
 * This class customize the result of the list for the feature controller.
 */
class FeatureHelperListBridge extends HelperListBridge
{
    /**
     * {@inheritDoc}
     */
    public function generateListQuery(
        HelperListConfiguration $helperListConfiguration,
        Request $request,
        int $idLang
    ): string {
        $listSql = parent::generateListQuery($helperListConfiguration, $request, $idLang);

        // adds feature_value count to evey row of feature
        foreach ($helperListConfiguration->list as &$featureRowRecord) {
            $this->addFeatureValuesCount($featureRowRecord);
        }

        return $listSql;
    }

    /**
     * Appends feature_value count to every record row
     *
     * @param array<string, mixed> $featureRowRecord
     */
    private function addFeatureValuesCount(array &$featureRowRecord): void
    {
        $query = new DbQuery();

        $query
            ->select('COUNT(fv.id_feature_value) as count_values')
            ->from('feature_value', 'fv')
            ->where('fv.id_feature =' . (int) $featureRowRecord['id_feature'])
            ->where('(fv.custom=0 OR fv.custom IS NULL)')
        ;

        $featureRowRecord['value'] = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
