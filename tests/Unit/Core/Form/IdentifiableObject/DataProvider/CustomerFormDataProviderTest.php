<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
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

    /**
     * Set up dependencies for CustomerFormDataProvider
     */
    public function setUp(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetCustomerForEditing::class))
            ->willReturn(
                new EditableCustomer(
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
                    false
                )
            )
        ;

        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->configuration
            ->method('get')
            ->will(
                $this->returnValueMap([
                    [
                        'PS_CUSTOMER_GROUP', 3,
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
        ], $customerFormDataProvider->getData(1));
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
        ], $customerFormDataProvider->getDefaultData());
    }
}
