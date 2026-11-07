<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Form\IdentifiableObject;

use Configuration;
use Context;
use DateTimeImmutable;
use Db;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\DiscountFormDataHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\DiscountFormDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

class DiscountPartialUseTest extends KernelTestCase
{
    private ContextMocker $contextMocker;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->contextMocker = (new ContextMocker())->mockContext();
        Context::getContext()->container = self::getContainer();
    }

    protected function tearDown(): void
    {
        $this->contextMocker->resetContext();
        parent::tearDown();
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables(['cart_rule', 'cart_rule_lang', 'cart_rule_shop']);
    }

    /**
     * @dataProvider getStoredAndExpectedFlag
     */
    public function testTheStoredFlagReachesTheForm(int $stored, bool $expected): void
    {
        $discountId = $this->createAmountDiscount($stored);

        $data = $this->getProvider()->getData($discountId);

        self::assertSame($expected, $data['value']['allow_partial_use']);
    }

    public function getStoredAndExpectedFlag(): iterable
    {
        yield 'not allowed' => [0, false];
        yield 'allowed' => [1, true];
    }

    /**
     * @dataProvider getSubmittedAndExpectedFlag
     */
    public function testWhatTheFormSubmitsIsStored(int $stored, bool $submitted, int $expected): void
    {
        $discountId = $this->createAmountDiscount($stored);

        $data = $this->getProvider()->getData($discountId);
        $data['value']['allow_partial_use'] = $submitted;
        $this->getHandler()->update($discountId, $data);

        self::assertSame(
            $expected,
            (int) Db::getInstance()->getValue(
                'SELECT partial_use FROM ' . _DB_PREFIX_ . 'cart_rule WHERE id_cart_rule = ' . $discountId
            )
        );
    }

    public function getSubmittedAndExpectedFlag(): iterable
    {
        yield 'turning it on' => [0, true, 1];
        yield 'turning it off' => [1, false, 0];
    }

    public function testANewDiscountStartsAllowingIt(): void
    {
        self::assertTrue($this->getProvider()->getDefaultData()['value']['allow_partial_use']);
    }

    private function getProvider(): DiscountFormDataProvider
    {
        return self::getContainer()->get(DiscountFormDataProvider::class);
    }

    private function getHandler(): DiscountFormDataHandler
    {
        return self::getContainer()->get(DiscountFormDataHandler::class);
    }

    private function createAmountDiscount(int $partialUse): int
    {
        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [(int) Configuration::get('PS_LANG_DEFAULT') => 'Amount discount']);
        $command
            ->setTotalQuantity(10)
            ->setQuantityPerUser(1)
            ->setValidityDateRange(new DateTimeImmutable('-1 day'), new DateTimeImmutable('+1 year'))
            ->setReductionAmount(new DecimalNumber('20'), (int) Configuration::get('PS_CURRENCY_DEFAULT'), true)
            ->setAllowPartialUse((bool) $partialUse)
        ;
        /** @var DiscountId $discountId */
        $discountId = self::getContainer()->get(CommandBusInterface::class)->handle($command);

        return $discountId->getValue();
    }
}
