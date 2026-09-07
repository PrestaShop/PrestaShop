<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CartRuleNameTooLongException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDiscountType;

/**
 * A name longer than the column used to reach CartRule::add() and come back as "An error occurred
 * during the CartRule creation", which says nothing about the name being the problem.
 */
class AddCartRuleToOrderCommandNameLengthTest extends TestCase
{
    public function testANameLongerThanTheColumnIsRejectedWithBothLengths(): void
    {
        $length = DiscountSettings::MAX_NAME_LENGTH + 1;

        try {
            $this->buildCommand(str_repeat('a', $length));
            $this->fail('A name longer than the maximum should have been rejected');
        } catch (CartRuleNameTooLongException $e) {
            $this->assertSame($length, $e->getGivenLength());
            $this->assertSame(DiscountSettings::MAX_NAME_LENGTH, $e->getMaxLength());
            $this->assertSame(OrderConstraintException::INVALID_CART_RULE_NAME, $e->getCode());
        }
    }

    public function testANameOfExactlyTheMaximumIsAccepted(): void
    {
        $name = str_repeat('a', DiscountSettings::MAX_NAME_LENGTH);

        $command = $this->buildCommand($name);

        $this->assertSame($name, $command->getCartRuleName());
    }

    /**
     * The limit is on characters, not bytes, so a multibyte name of the allowed length must pass.
     */
    public function testAMultibyteNameOfTheMaximumLengthIsAccepted(): void
    {
        $name = str_repeat('é', DiscountSettings::MAX_NAME_LENGTH);

        $command = $this->buildCommand($name);

        $this->assertSame($name, $command->getCartRuleName());
    }

    public function testAnEmptyNameIsStillRejectedAsBefore(): void
    {
        $this->expectException(OrderConstraintException::class);

        $this->buildCommand('');
    }

    private function buildCommand(string $name): AddCartRuleToOrderCommand
    {
        return new AddCartRuleToOrderCommand(1, $name, OrderDiscountType::DISCOUNT_AMOUNT, '10');
    }
}
