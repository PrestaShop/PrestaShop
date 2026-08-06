<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\OrderMessage\CommandHandler;

use ErrorException;
use OrderMessage;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class EditOrderMessageHandlerTest extends KernelTestCase
{
    /**
     * @var object|CommandBusInterface|null
     */
    private $commandBus;

    /**
     * @var int
     */
    private $defaultLangId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['order_message', 'order_message_lang']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['order_message', 'order_message_lang']);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->defaultLangId = (int) self::getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT');
    }

    /**
     * A partial edit that changes only the message (leaving the localized name null)
     * must NOT run the name-uniqueness check: that check iterates over the localized
     * name with foreach(), which emits a warning when the name is null. The edit must
     * complete without warning, the name must stay untouched and the message updated.
     */
    public function testEditingOnlyTheMessageKeepsTheNameAndDoesNotTriggerTheNameCheck(): void
    {
        /** @var OrderMessageId $orderMessageId */
        $orderMessageId = $this->commandBus->handle(new AddOrderMessageCommand(
            [$this->defaultLangId => 'Delivery delay'],
            [$this->defaultLangId => 'Your order is delayed.']
        ));

        // Fail loudly if the partial edit emits a PHP warning — the name-uniqueness
        // check doing foreach() over the null localized name is exactly such a warning.
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (E_WARNING === $severity) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }

            return false;
        });

        try {
            // Partial edit: only the message is supplied, the localized name is null.
            $this->commandBus->handle(new EditOrderMessageCommand(
                $orderMessageId->getValue(),
                null,
                [$this->defaultLangId => 'Your order is delayed by 48h.']
            ));
        } finally {
            restore_error_handler();
        }

        $orderMessage = new OrderMessage($orderMessageId->getValue());
        $this->assertSame('Delivery delay', $orderMessage->name[$this->defaultLangId]);
        $this->assertSame('Your order is delayed by 48h.', $orderMessage->message[$this->defaultLangId]);
    }
}
