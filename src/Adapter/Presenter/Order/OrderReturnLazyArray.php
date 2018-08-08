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

use Link;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShopException;
use Tools;

class OrderReturnLazyArray extends AbstractLazyArray
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Link
     */
    private $link;

    /** @var array */
    private $orderReturn;

    /**
     * OrderReturnLazyArray constructor.
     * @param string $prefix
     * @param Link $link
     * @param array $orderReturn
     * @throws \ReflectionException
     */
    public function __construct($prefix, Link $link, $orderReturn)
    {
        $this->prefix = $prefix;
        $this->link = $link;
        $this->orderReturn = $orderReturn;
        parent::__construct();
        $this->appendArray($orderReturn);
    }

    /**
     * @arrayAccess
     * @return mixed
     */
    public function getId()
    {
        return $this->orderReturn['id_order_return'];
    }

    /**
     * @arrayAccess
     * @return string
     */
    public function getDetailsUrl()
    {
        return $this->link->getPageLink(
            'order-detail',
            true,
            null,
            'id_order='.(int) $this->orderReturn['id_order']
        );
    }

    /**
     * @arrayAccess
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->link->getPageLink(
            'order-return',
            true,
            null,
            'id_order_return='.(int) $this->orderReturn['id_order_return']
        );
    }

    /**
     * @arrayAccess
     * @return string
     */
    public function getReturnNumber()
    {
        return $this->prefix.sprintf('%06d', $this->orderReturn['id_order_return']);
    }

    /**
     * @arrayAccess
     * @return string
     * @throws PrestaShopException
     */
    public function getReturnDate()
    {
        return Tools::displayDate($this->orderReturn['date_add'], null, false);
    }

    /**
     * @arrayAccess
     * @return string
     */
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
