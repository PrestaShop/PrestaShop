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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use Carrier;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * This class will provide data from DB / ORM about Category.
 */
class CarrierDataProvider
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get all carriers in a given language.
     *
     * @param int $id_lang Language id
     * @param bool $active Returns only active carriers when true
     * @param bool $delete
     * @param bool|int $id_zone
     * @param string|null $ids_group
     * @param int $modules_filters Possible values:
     *                             - PS_CARRIERS_ONLY
     *                             - CARRIERS_MODULE
     *                             - CARRIERS_MODULE_NEED_RANGE
     *                             - PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
     *                             - ALL_CARRIERS
     *
     * @return array Carriers
     */
    public function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = Carrier::PS_CARRIERS_ONLY)
    {
        return Carrier::getCarriers($id_lang, $active, $delete, $id_zone, $ids_group, $modules_filters);
    }

    /**
     * Get all active carriers in given language, usable for choice form type.
     *
     * @param int|null $languageId if not provided - will use the default language
     *
     * @return array carrier choices
     */
    public function getActiveCarriersChoices($languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->configuration->getInt('PS_LANG_DEFAULT');
        }

        $carriers = $this->getCarriers($languageId, true, false, false, null, $this->getAllCarriersConstant());
        $carriersChoices = [];

        foreach ($carriers as $carrier) {
            $choiceId = (int) $carrier['id_carrier'] . ' - ' . $carrier['name'];
            if (!empty($carrier['delay'])) {
                $choiceId .= ' (' . $carrier['delay'] . ')';
            }

            $carriersChoices[$choiceId] = (int) $carrier['id_carrier'];
        }

        return $carriersChoices;
    }

    /**
     * Get carriers order by choices.
     *
     * @return array order by choices
     */
    public function getOrderByChoices()
    {
        return [
            'Price' => Carrier::SORT_BY_PRICE,
            'Position' => Carrier::SORT_BY_POSITION,
        ];
    }

    /**
     * Get carriers order way choices.
     *
     * @return array order way choices
     */
    public function getOrderWayChoices()
    {
        return [
            'Ascending' => Carrier::SORT_BY_ASC,
            'Descending' => Carrier::SORT_BY_DESC,
        ];
    }

    /**
     * Get the CarrierCore class ALL_CARRIERS constant value.
     *
     * @return int
     */
    public function getAllCarriersConstant()
    {
        return Carrier::ALL_CARRIERS;
    }
}
