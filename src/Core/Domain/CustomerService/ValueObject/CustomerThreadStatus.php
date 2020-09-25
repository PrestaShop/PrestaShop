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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * Status that customer thread can have
 */
final class CustomerThreadStatus
{
    const OPEN = 'open';
    const CLOSED = 'closed';
    const PENDING_1 = 'pending1';
    const PENDING_2 = 'pending2';

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $availableStatuses = [
            self::OPEN,
            self::CLOSED,
            self::PENDING_1,
            self::PENDING_2,
        ];

        if (!in_array($status, $availableStatuses)) {
            throw new CustomerServiceException(sprintf('Customer thread status "%s" is not defined, available statuses are "%s"', $status, implode(',', $availableStatuses)));
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->status;
    }
}
