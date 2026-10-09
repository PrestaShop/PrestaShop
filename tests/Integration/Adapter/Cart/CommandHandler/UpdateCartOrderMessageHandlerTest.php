<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart\CommandHandler;

use Cart;
use Db;
use Message;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartOrderMessageCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A back office order is prepared on a cart that has no order yet, so the order message typed while
 * preparing it has nowhere to live until the order is created. It belongs to the cart in the
 * meantime - `Message` already reads it from there, nothing ever wrote it.
 */
class UpdateCartOrderMessageHandlerTest extends KernelTestCase
{
    private static int $cartId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $cart = new Cart();
        $cart->id_customer = 1;
        $cart->id_currency = 1;
        $cart->id_lang = 1;
        $cart->id_shop = 1;
        $cart->id_shop_group = 1;
        $cart->add();
        self::$cartId = (int) $cart->id;
    }

    public static function tearDownAfterClass(): void
    {
        Db::getInstance()->delete('message', 'id_cart = ' . self::$cartId);
        Db::getInstance()->delete('cart', 'id_cart = ' . self::$cartId);
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Db::getInstance()->delete('message', 'id_cart = ' . self::$cartId);
    }

    public function testTheMessageIsStoredOnACartThatHasNoOrderYet(): void
    {
        $this->dispatch('Call the customer before shipping');

        $stored = Message::getMessageByCartId(self::$cartId);

        self::assertNotEmpty($stored, 'nothing was stored for the cart');
        self::assertSame('Call the customer before shipping', $stored['message']);
    }

    public function testWritingAgainReplacesTheDraftInsteadOfAddingAnother(): void
    {
        $this->dispatch('First draft');
        $this->dispatch('Second draft');

        self::assertSame(1, $this->countMessages(), 'a second row was created for the same cart');
        self::assertSame('Second draft', Message::getMessageByCartId(self::$cartId)['message']);
    }

    public function testClearingTheDraftRemovesIt(): void
    {
        $this->dispatch('Something');
        $this->dispatch('');

        self::assertSame(0, $this->countMessages(), 'the emptied draft was left behind');
    }

    private function dispatch(string $orderMessage): void
    {
        self::getContainer()->get('prestashop.core.command_bus')->handle(
            new UpdateCartOrderMessageCommand(self::$cartId, $orderMessage)
        );
    }

    private function countMessages(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'message WHERE id_cart = ' . self::$cartId
        );
    }
}
