<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\EventListener\Console;

use Configuration;
use Context;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * The currency has to be there once any command starts, as it is for a back office request: legacy code
 * run by a command reads its precision, for instance when an order status change creates an invoice.
 */
class CurrencyContextCommandListenerTest extends KernelTestCase
{
    public function testACommandStartsWithTheDefaultCurrency(): void
    {
        $context = Context::getContext();
        $previousCurrency = $context->currency;
        // What bin/console leaves after including config/config.inc.php, which sets no currency
        $context->currency = null;

        try {
            $application = new Application(self::bootKernel());
            $application->setAutoExit(false);
            // Run through the application, not a CommandTester: the listener is a console.command one
            $exitCode = $application->run(new ArrayInput(['command' => 'list']), new NullOutput());

            $this->assertSame(0, $exitCode);
            $this->assertNotNull($context->currency);
            $this->assertSame((int) Configuration::get('PS_CURRENCY_DEFAULT'), (int) $context->currency->id);
        } finally {
            $context->currency = $previousCurrency;
        }
    }
}
