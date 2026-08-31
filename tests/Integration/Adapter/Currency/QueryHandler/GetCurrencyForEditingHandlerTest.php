<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\EditableCurrency;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetCurrencyForEditingHandlerTest extends KernelTestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');
    }

    /**
     * The "enabled" state is read from the database where PDO returns the
     * TINYINT(1) column as a string ("0"/"1"). EditableCurrency exposes it as a
     * boolean (isEnabled(): @return bool) and strictly-typed API resources rely
     * on it being a real bool, so the handler must cast it.
     */
    public function testEnabledStateIsExposedAsBoolean(): void
    {
        /** @var EditableCurrency $result */
        $result = $this->queryBus->handle(new GetCurrencyForEditing(1));

        $this->assertIsBool($result->isEnabled());
    }
}
