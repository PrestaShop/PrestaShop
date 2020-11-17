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

namespace Tests\Unit\Adapter\Order\CommandHandler;

use Mockery;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\CommandHandler\SetInternalOrderNoteHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SetInternalOrderNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class SetInternalOrderNoteHandlerTest extends TestCase
{
    public function testHandleValidSetInternalOrderNoteCommand()
    {
        $mock = Mockery::mock(\Order::class);
        $mock->shouldReceive('validateFields')
            ->andReturnTrue();
        $mock->shouldReceive('update')
            ->andReturnTrue();

        $handler = new ExtendedForTestSetInternalOrderNoteHandler();
        $handler->setMock($mock);

        $handler->handle(new SetInternalOrderNoteCommand(1, 'a'));
    }

    public function testHandleSetInternalOrderNoteCommandWithNotValidFields()
    {
        $mock = Mockery::mock(\Order::class);
        $mock->shouldReceive('validateFields')
            ->andReturnFalse();

        $handler = new ExtendedForTestSetInternalOrderNoteHandler();
        $handler->setMock($mock);

        $this->expectException(OrderConstraintException::class);

        $handler->handle(new SetInternalOrderNoteCommand(1, 'a'));
    }

    public function testHandleSetInternalOrderNoteCommandButUpdateFails()
    {
        $mock = Mockery::mock(\Order::class);
        $mock->shouldReceive('validateFields')
            ->andReturnTrue();
        $mock->shouldReceive('update')
            ->andReturnFalse();

        $handler = new ExtendedForTestSetInternalOrderNoteHandler();
        $handler->setMock($mock);

        $this->expectException(OrderException::class);

        $handler->handle(new SetInternalOrderNoteCommand(1, 'a'));
    }
}

/**
 * This class extends the target of this test SetInternalOrderNoteHandler
 * in order to rewrite the parts of it that rely on legacy logic
 * and cannot be easily mocked
 */
class ExtendedForTestSetInternalOrderNoteHandler extends SetInternalOrderNoteHandler
{
    private $mock;

    public function setMock($mock)
    {
        $this->mock = $mock;
    }

    public function getOrder(OrderId $orderId)
    {
        return $this->mock;
    }
}
