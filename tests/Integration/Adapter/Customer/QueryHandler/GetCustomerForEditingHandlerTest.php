<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use Tests\TestCase\SymfonyIntegrationTestCase;

class GetCustomerForEditingHandlerTest extends SymfonyIntegrationTestCase
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
    }

    public function testItUsesCustomerShopIdInEditableCustomer(): void
    {
        $customerId = 1;
        $legacyCustomer = new Customer($customerId);

        /** @var EditableCustomer $editableCustomer */
        $editableCustomer = $this->commandBus->handle(new GetCustomerForEditing($customerId));

        $this->assertInstanceOf(EditableCustomer::class, $editableCustomer);
        $this->assertSame((int) $legacyCustomer->id_shop, $editableCustomer->getShopId());
    }
}
