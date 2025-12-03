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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Context;
use Country;
use Db;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Choices for countries in which at least one order has been placed
 */
final class OrderCountriesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        if (!Country::isCurrentlyUsed('country', true)) {
            return [];
        }

        $countries = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cl.id_country, cl.`name`
            FROM `' . _DB_PREFIX_ . 'country_lang` cl
            WHERE EXISTS (
                SELECT 1
                FROM `' . _DB_PREFIX_ . 'orders` o
                INNER JOIN `' . _DB_PREFIX_ . 'address` a
			        ON a.id_address = o.id_address_delivery
                WHERE a.id_country = cl.id_country
            ) AND cl.`id_lang` = ' . (int) Context::getContext()->language->id . '
			ORDER BY cl.name ASC'
        );

        return FormChoiceFormatter::formatFormChoices(
            $countries,
            'id_country',
            'name'
        );
    }
}
