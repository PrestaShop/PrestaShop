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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\FormHelper;
use Symfony\Component\Form\AbstractType;

/**
 * This subclass contains common functions for specific Form types needs.
 *
 * @deprecated since 9.0 use \Symfony\Component\Form\AbstractType instead
 */
abstract class CommonAbstractType extends AbstractType
{
    /**
     * @deprecated since 9.0
     */
    public const PRESTASHOP_DECIMALS = FormHelper::DEFAULT_PRICE_PRECISION;

    /**
     * @deprecated since 9.0
     */
    public const PRESTASHOP_WEIGHT_DECIMALS = 6;

    /**
     * Format legacy data list to mapping SF2 form field choice.
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     *
     * @return array
     */
    protected function formatDataChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0 and will be removed in the next major version. There is no replacement for this method.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        $new_list = [];
        foreach ($list as $item) {
            if (array_key_exists($item[$mapping_name], $new_list)) {
                return self::formatDataDuplicateChoicesList($list, $mapping_value, $mapping_name);
            } else {
                $new_list[$item[$mapping_name]] = $item[$mapping_value];
            }
        }

        return $new_list;
    }

    /**
     * Format legacy data list to mapping SF2 form field choice (possibility to have 2 name equals).
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     *
     * @return array
     */
    protected function formatDataDuplicateChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0 and will be removed in the next major version. There is no replacement for this method.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        $new_list = [];
        foreach ($list as $item) {
            $new_list[$item[$mapping_value] . ' - ' . $item[$mapping_name]] = $item[$mapping_value];
        }

        return $new_list;
    }
}
