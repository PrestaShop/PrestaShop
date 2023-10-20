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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Status;

/**
 * Defines colors for order statuses
 */
class CustomerThreadStatusColor
{
    /**
     * Used for status when customer thread is open.
     */
    public const OPENED = '#01B887';

    /**
     * Used for statuses when customer thread is closed.
     * Example statuses: Processing in progress, On backorder (paid), Payment accepted.
     */
    public const CLOSED = '#2C3E50';

    /**
     * Used for status when customer thread is pending_1.
     */
    public const PENDING_1 = '#3498D8';

    /**
     * Used for status when customer thread is pending_2.
     */
    public const PENDING_2 = '#34209E';

    public const CUSTOMER_THREAD_STATUSES = [
        'open' => self::OPENED,
        'closed' => self::CLOSED,
        'pending1' => self::PENDING_1,
        'pending2' => self::PENDING_2,
    ];

    /**
     * Class is not meant to be initialized.
     */
    private function __construct()
    {
    }
}
