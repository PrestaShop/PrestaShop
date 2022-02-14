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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

class BulkCombinationFormDataProvider implements FormDataProviderInterface
{
    public function getDefaultData()
    {
        return [
            'disabling_toggle_reference' => false,
            'disabling_toggle_price_tax_excluded' => false,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        return [
            //@todo: all of this smells :(
            // This form is not handled before submit as usually, so data is always empty in PRE_SUBMIT event
            // That is why these values must be truthy or else PRE_SUBMIT event will always disable the related inputs
            'disabling_toggle_reference' => true,
            'disabling_toggle_price_tax_excluded' => true,
        ];
    }
}
