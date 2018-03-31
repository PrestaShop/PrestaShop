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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Exception;
use Link;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyPresenter;
use Tools;

class OrderReturnPresenter extends AbstractLazyPresenter
{
    private $prefix;
    private $link;
    /** @var array */
    private $orderReturn;

    public function __construct($prefix, Link $link)
    {
        $this->prefix = $prefix;
        $this->link = $link;
        parent::__construct();
    }

    public function present($orderReturn)
    {
        if (!is_array($orderReturn)) {
            throw new Exception('orderReturnPresenter can only present order_return passed as array');
        }

        $this->orderReturn = $orderReturn;

        return clone($this);
    }

    public function getId()
    {
        return $this->orderReturn['id_order_return'];
    }

    public function getDetailsUrl()
    {
        return $this->link->getPageLink(
            'order-detail',
            true,
            null,
            'id_order='.(int) $this->orderReturn['id_order']
        );
    }

    public function getReturnUrl()
    {
        return $this->link->getPageLink(
            'order-return',
            true,
            null,
            'id_order_return='.(int) $this->orderReturn['id_order_return']
        );
    }


    public function getReturnNumber()
    {
        return $this->prefix.sprintf('%06d', $this->orderReturn['id_order_return']);
    }

    public function getReturnDate()
    {
        return Tools::displayDate($this->orderReturn['date_add'], null, false);
    }

    public function getPrintUrl()
    {
        return ($this->orderReturn['state'] == 2)
            ? $this->link->getPageLink(
                'pdf-order-return',
                true,
                null,
                'id_order_return='.(int) $this->orderReturn['id_order_return']
            )
            : '';
    }
}
