<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Discount\CommandHandler;

use Db;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * A minimum amount says whether the shipping total counts towards it, and CartRule::checkValidity()
 * adds that total only when it does. The discount form has had no field for it since #40809, so its
 * data handler sends false; a caller that omits the argument instead - which is what the Admin API
 * does when minimumAmount carries only amount, currency and tax - must land on the same value, not
 * the opposite one.
 */
class DiscountMinimumAmountShippingTest extends KernelTestCase
{
    private CommandBusInterface $commandBus;

    private int $defaultLangId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['cart_rule', 'cart_rule_lang', 'cart_rule_shop']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['cart_rule', 'cart_rule_lang', 'cart_rule_shop']);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->defaultLangId = (int) self::getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT');
    }

    public function testAMinimumAmountWithNoShippingInclusionGivenExcludesShipping(): void
    {
        $discountId = $this->addDiscount('No shipping inclusion given', function (AddDiscountCommand $command): void {
            $command->setMinimumAmount(new DecimalNumber('50'), 1, true);
        });

        $this->assertSame(0, $this->storedShippingInclusion($discountId), 'the schema default for minimum_amount_shipping is 0');
    }

    public function testAMinimumAmountCanStillIncludeShippingWhenItIsAskedFor(): void
    {
        $discountId = $this->addDiscount('Shipping included', function (AddDiscountCommand $command): void {
            $command->setMinimumAmount(new DecimalNumber('50'), 1, true, true);
        });

        $this->assertSame(1, $this->storedShippingInclusion($discountId));
    }

    /**
     * An update that restates the amount without restating the shipping inclusion must not silently
     * turn it on: the condition it writes has to be the one the caller described.
     */
    public function testUpdatingTheAmountWithNoShippingInclusionGivenExcludesShipping(): void
    {
        $discountId = $this->addDiscount('Updated amount', function (AddDiscountCommand $command): void {
            $command->setMinimumAmount(new DecimalNumber('50'), 1, true, true);
        });
        $this->assertSame(1, $this->storedShippingInclusion($discountId));

        $update = new UpdateDiscountCommand($discountId);
        $update->setMinimumAmount(new DecimalNumber('80'), 1, true);
        $this->commandBus->handle($update);

        $this->assertSame(0, $this->storedShippingInclusion($discountId));
    }

    private function addDiscount(string $name, callable $configure): int
    {
        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [$this->defaultLangId => $name]);
        $command->setReductionPercent(new DecimalNumber('10'));
        $configure($command);

        /** @var DiscountId $id */
        $id = $this->commandBus->handle($command);

        return $id->getValue();
    }

    private function storedShippingInclusion(int $discountId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `minimum_amount_shipping` FROM ' . _DB_PREFIX_ . 'cart_rule WHERE `id_cart_rule` = ' . $discountId
        );
    }
}
