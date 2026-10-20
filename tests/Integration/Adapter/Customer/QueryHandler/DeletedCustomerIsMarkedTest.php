<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer\QueryHandler;

use Configuration;
use Context;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A soft deleted customer is hidden from the Customers grid but its view page stays reachable, because
 * the handler only checks that the record loads. The page therefore has to say the account is deleted:
 * `deleted` and `active` are independent columns, so without it the page reports a deleted customer as
 * Active, which is worse than saying nothing.
 */
class DeletedCustomerIsMarkedTest extends KernelTestCase
{
    private ?Customer $customer = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        // The handler formats cart totals, which needs a currency on the context; a bare kernel has none.
        $context = Context::getContext();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->container = self::getContainer();
    }

    protected function tearDown(): void
    {
        if (null !== $this->customer && $this->customer->id) {
            $this->customer->delete();
            $this->customer = null;
        }

        parent::tearDown();
    }

    public function testADeletedCustomerIsReportedAsDeletedWhileStayingActive(): void
    {
        $customer = $this->createCustomer();

        self::assertFalse($this->view($customer)->getPersonalInformation()->isDeleted(), 'a fresh customer is not deleted');

        $customer->deleted = true;
        $customer->update();

        $personalInformation = $this->view($customer)->getPersonalInformation();

        self::assertTrue($personalInformation->isDeleted(), 'the view has to report the deletion');
        self::assertTrue(
            $personalInformation->isActive(),
            'deleted and active are independent columns, so deleting must not be inferred from active'
        );
    }

    private function view(Customer $customer): ViewableCustomer
    {
        return self::getContainer()
            ->get('prestashop.core.query_bus')
            ->handle(new GetCustomerForViewing((int) $customer->id));
    }

    private function createCustomer(): Customer
    {
        $customer = new Customer();
        $customer->firstname = 'Deleted';
        $customer->lastname = 'Probe';
        $customer->email = sprintf('deleted-probe-%s@example.com', uniqid());
        // the column stores a hash, not a password: Customer::$definition validates it with isHashedPassword
        $customer->passwd = (new Hashing())->hash('probe-password');
        $customer->active = true;
        $customer->add();

        return $this->customer = $customer;
    }
}
