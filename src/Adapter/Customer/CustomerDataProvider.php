<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Customer;

use Symfony\Component\Process\Exception\LogicException;
use Customer;

/**
 * This class will provide data from DB / ORM about Customer
 */
class CustomerDataProvider
{
    /**
     * Get a customer
     *
     * @param int $id
     *
     * @throws LogicException If the customer id is not set
     *
     * @return object customer
     */
    public function getCustomer($id)
    {
        if (!$id) {
            throw new LogicException('You need to provide a customer id', null, 5002);
        }

        $customer = new Customer($id);
        return $customer;
    }
}
