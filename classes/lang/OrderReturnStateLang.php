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

class OrderReturnStateLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Orderscustomers.Feature';

    protected $keys = array('id_order_return_state');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Waiting for confirmation') => $this->translator->trans('Waiting for confirmation', array(), 'Admin.Orderscustomers.Feature', $this->locale),
                md5('Waiting for package') => $this->translator->trans('Waiting for package', array(), 'Admin.Orderscustomers.Feature', $this->locale),
                md5('Package received') => $this->translator->trans('Package received', array(), 'Admin.Orderscustomers.Feature', $this->locale),
                md5('Return denied') => $this->translator->trans('Return denied', array(), 'Admin.Orderscustomers.Feature', $this->locale),
                md5('Return completed') => $this->translator->trans('Return completed', array(), 'Admin.Orderscustomers.Feature', $this->locale),
            ),
        );
    }
}
