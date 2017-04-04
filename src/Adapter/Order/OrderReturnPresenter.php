<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use Exception;
use Link;
use Tools;

class OrderReturnPresenter implements PresenterInterface
{
    private $prefix;
    private $link;

    public function __construct($prefix, Link $link)
    {
        $this->prefix = $prefix;
        $this->link = $link;
    }

    public function present($orderReturn)
    {
        if (!is_array($orderReturn)) {
            throw new Exception('orderReturnPresenter can only present order_return passed as array');
        }

        $presentedOrderReturn = $orderReturn;
        $presentedOrderReturn['id'] = $orderReturn['id_order_return'];

        $presentedOrderReturn['return_number'] = $this->prefix.sprintf('%06d', $orderReturn['id_order_return']);
        $presentedOrderReturn['return_date'] = Tools::displayDate($orderReturn['date_add'], null, false);
        $presentedOrderReturn['print_url'] = ($orderReturn['state'] == 2)
            ? $this->link->getPageLink('pdf-order-return', true, null, 'id_order_return='.(int) $orderReturn['id_order_return'])
            : '';
        $presentedOrderReturn['details_url'] = $this->link->getPageLink(
            'order-detail', true, null, 'id_order='.(int) $orderReturn['id_order']
        );
        $presentedOrderReturn['return_url'] = $this->link->getPageLink(
            'order-return', true, null, 'id_order_return='.(int) $orderReturn['id_order_return']
        );

        return $presentedOrderReturn;
    }
}
