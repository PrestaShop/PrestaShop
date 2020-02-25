<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\CustomerService\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;

class CustomerThreadStatusTest extends TestCase
{
    /**
     * @dataProvider getValidCustomerThreadStatuses
     */
    public function testCanBeCreatedWithValidStatus($customerThreadStatus)
    {
        $status = new CustomerThreadStatus($customerThreadStatus);

        $this->assertEquals($customerThreadStatus, $status->getValue());
    }

    /**
     * @dataProvider getInvalidCustomerThreadStatuses
     */
    public function testThrowsExceptionWhenCreatingWithInvalidStatus($invalidCustomerThreadStatus)
    {
        $this->expectException(CustomerServiceException::class);

        new CustomerThreadStatus($invalidCustomerThreadStatus);
    }

    public function getValidCustomerThreadStatuses()
    {
        yield [CustomerThreadStatus::PENDING_1];
        yield [CustomerThreadStatus::PENDING_2];
        yield [CustomerThreadStatus::OPEN];
        yield [CustomerThreadStatus::CLOSED];
    }

    public function getInvalidCustomerThreadStatuses()
    {
        yield [1];
        yield ['opened'];
    }
}
