<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Query\GetCustomerTradeCredit;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\CustomerTradeCreditSummary;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CustomerFormDataProvider;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroup;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroups;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;

class CustomerFormDataProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var DefaultGroupsProviderInterface
     */
    private $defaultGroupsProvider;

    private bool $isGuest = false;

    private ?string $tradeCreditBalance = '40000';

    /**
     * Set up dependencies for CustomerFormDataProvider
     */
    public function setUp(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->willReturnCallback(function ($query) {
                if ($query instanceof GetCustomerTradeCredit) {
                    return new CustomerTradeCreditSummary(
                        null === $this->tradeCreditBalance ? null : new DecimalNumber($this->tradeCreditBalance),
                        new DecimalNumber('50000'),
                        'PLN',
                        []
                    );
                }
                $this->assertInstanceOf(GetCustomerForEditing::class, $query);

                return new EditableCustomer(
                    new CustomerId(1),
                    2,
                    new FirstName('Firstname'),
                    new LastName('Lastname'),
                    new Email('firstname.lastname@prestashop.com'),
                    new Birthday('1990-01-01'),
                    true,
                    true,
                    true,
                    [1, 2, 3],
                    3,
                    'Demo company',
                    'siret_code',
                    'ape_code',
                    'prestashop.com',
                    36.99,
                    10,
                    1,
                    $this->isGuest
                );
            })
        ;

        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->configuration
            ->method('get')
            ->will(
                $this->returnValueMap([
                    [
                        'PS_CUSTOMER_GROUP', 3,
                    ],
                    [
                        'PS_TRADE_CREDIT_DEFAULT_AMOUNT', '50000',
                    ],
                ])
            )
        ;

        $this->defaultGroupsProvider = $this->createMock(DefaultGroupsProviderInterface::class);
        $this->defaultGroupsProvider
            ->method('getGroups')
            ->willReturn(new DefaultGroups(
                new DefaultGroup(1, 'Visitors'),
                new DefaultGroup(2, 'Guests'),
                new DefaultGroup(3, 'Customers')
            ))
        ;
    }

    public function testItProvidesCorrectFormDataWithB2bFeatureBeingOff()
    {
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            false
        );

        $this->assertEquals([
            'gender_id' => 2,
            'first_name' => 'Firstname',
            'last_name' => 'Lastname',
            'email' => 'firstname.lastname@prestashop.com',
            'birthday' => '1990-01-01',
            'is_enabled' => true,
            'is_partner_offers_subscribed' => true,
            'group_ids' => [1, 2, 3],
            'default_group_id' => 3,
            'is_guest' => false,
            'is_newsletter_subscribed' => true,
            'trade_credit_amount' => '40000.00',
            'trade_credit_expected_amount' => '40000.00',
        ], $customerFormDataProvider->getData(1));
    }

    public function testItProvidesCorrectFormDataWithB2bFeatureBeingOn()
    {
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            true
        );

        $this->assertEquals([
            'gender_id' => 2,
            'first_name' => 'Firstname',
            'last_name' => 'Lastname',
            'email' => 'firstname.lastname@prestashop.com',
            'birthday' => '1990-01-01',
            'is_enabled' => true,
            'is_partner_offers_subscribed' => true,
            'group_ids' => [1, 2, 3],
            'default_group_id' => 3,
            'company_name' => 'Demo company',
            'siret_code' => 'siret_code',
            'ape_code' => 'ape_code',
            'website' => 'prestashop.com',
            'allowed_outstanding_amount' => 36.99,
            'max_payment_days' => 10,
            'risk_id' => 1,
            'is_guest' => false,
            'is_newsletter_subscribed' => true,
            'trade_credit_amount' => '40000.00',
            'trade_credit_expected_amount' => '40000.00',
        ], $customerFormDataProvider->getData(1));
    }

    public function testGetDataForNotGrantedTradeCreditUsesDefault()
    {
        $this->tradeCreditBalance = null;
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            false
        );

        $data = $customerFormDataProvider->getData(1);

        $this->assertSame('50000.00', $data['trade_credit_amount']);
        $this->assertSame('50000.00', $data['trade_credit_expected_amount']);
    }

    public function testGetDataForGuestOmitsTradeCredit()
    {
        $this->isGuest = true;
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            false
        );

        $data = $customerFormDataProvider->getData(1);

        $this->assertArrayNotHasKey('trade_credit_amount', $data);
        $this->assertArrayNotHasKey('trade_credit_expected_amount', $data);
    }

    public function testItProvidesCorrectDefaultDataWhenB2bFeatureIsOff()
    {
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            false
        );

        $this->assertEquals([
            'is_enabled' => true,
            'is_partner_offers_subscribed' => false,
            'group_ids' => [1, 2, 3],
            'default_group_id' => 3,
            'is_guest' => false,
            'trade_credit_amount' => '50000.00',
            'trade_credit_expected_amount' => '50000.00',
        ], $customerFormDataProvider->getDefaultData());
    }

    public function testItProvidesAdditionalDefaultDataWhenB2bFeatureIsOn()
    {
        $customerFormDataProvider = new CustomerFormDataProvider(
            $this->queryBus,
            $this->configuration,
            $this->defaultGroupsProvider,
            true
        );

        $this->assertEquals([
            'is_enabled' => true,
            'is_partner_offers_subscribed' => false,
            'group_ids' => [1, 2, 3],
            'default_group_id' => 3,
            'allowed_outstanding_amount' => 0,
            'max_payment_days' => 0,
            'is_guest' => false,
            'trade_credit_amount' => '50000.00',
            'trade_credit_expected_amount' => '50000.00',
        ], $customerFormDataProvider->getDefaultData());
    }
}
