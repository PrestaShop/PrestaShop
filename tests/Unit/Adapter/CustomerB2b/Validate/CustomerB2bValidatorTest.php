<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\CustomerB2b\Validate;

use Customer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate\CustomerB2bValidator;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use PrestaShopBundle\Entity\Repository\CustomerB2bRepository;
use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;

class CustomerB2bValidatorTest extends TestCase
{
    private const BUSINESS_ENTITY_ID = 7;
    private const BUSINESS_ENTITY_SHOP_ID = 1;
    private const BUSINESS_ENTITY_SHOP_GROUP_ID = 1;
    private const OTHER_SHOP_ID = 2;
    private const OTHER_SHOP_GROUP_ID = 2;

    private BusinessEntity $businessEntity;
    private B2bRole $role;

    protected function setUp(): void
    {
        $this->businessEntity = $this->createConfiguredMock(BusinessEntity::class, [
            'getId' => self::BUSINESS_ENTITY_ID,
            'getIdShop' => self::BUSINESS_ENTITY_SHOP_ID,
        ]);
        $this->role = new B2bRole();
    }

    public static function getMatchingShops(): iterable
    {
        yield 'the business entity shop' => [self::BUSINESS_ENTITY_SHOP_ID, self::BUSINESS_ENTITY_SHOP_GROUP_ID, false];
        yield 'another shop of a group sharing customers' => [self::OTHER_SHOP_ID, self::BUSINESS_ENTITY_SHOP_GROUP_ID, true];
    }

    public static function getMismatchedShops(): iterable
    {
        yield 'another shop of a group not sharing customers' => [self::OTHER_SHOP_ID, self::BUSINESS_ENTITY_SHOP_GROUP_ID, false];
        yield 'a shop of a different group' => [self::OTHER_SHOP_ID, self::OTHER_SHOP_GROUP_ID, true];
    }

    #[DataProvider('getMatchingShops')]
    public function testItAcceptsCustomerFromMatchingShop(int $customerShopId, int $customerShopGroupId, bool $groupSharesCustomers): void
    {
        $validator = $this->buildValidator($this->mockCustomer($customerShopId, $customerShopGroupId), $groupSharesCustomers);

        $result = $validator->validate($this->buildCommand());

        $this->assertSame($this->businessEntity, $result->businessEntity);
        $this->assertSame($this->role, $result->role);
    }

    #[DataProvider('getMismatchedShops')]
    public function testItRejectsCustomerFromMismatchedShop(int $customerShopId, int $customerShopGroupId, bool $groupSharesCustomers): void
    {
        $validator = $this->buildValidator($this->mockCustomer($customerShopId, $customerShopGroupId), $groupSharesCustomers);

        $this->expectException(CannotAddCustomerB2bException::class);
        $this->expectExceptionCode(CannotAddCustomerB2bException::SHOP_MISMATCH);

        $validator->validate($this->buildCommand());
    }

    private function mockCustomer(int $shopId, int $shopGroupId): Customer
    {
        $customer = $this->createMock(Customer::class);
        $customer->id = 42;
        $customer->id_shop = $shopId;
        $customer->id_shop_group = $shopGroupId;

        return $customer;
    }

    private function buildValidator(Customer $customer, bool $groupSharesCustomers): CustomerB2bValidator
    {
        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->method('get')->willReturn($customer);

        $customerB2bRepository = $this->createMock(CustomerB2bRepository::class);
        $customerB2bRepository->method('findByCustomerId')->willReturn(null);

        $businessEntityRepository = $this->createMock(BusinessEntityRepository::class);
        $businessEntityRepository->method('findById')->willReturn($this->businessEntity);

        $b2bRoleRepository = $this->createMock(B2bRoleRepository::class);
        $b2bRoleRepository->method('find')->willReturn($this->role);

        $businessEntityShop = $this->createConfiguredMock(Shop::class, [
            'getShopGroup' => $this->createConfiguredMock(ShopGroup::class, [
                'getId' => self::BUSINESS_ENTITY_SHOP_GROUP_ID,
                'getShareCustomer' => $groupSharesCustomers,
            ]),
        ]);

        $shopRepository = $this->createMock(ShopRepository::class);
        $shopRepository->method('find')->with(self::BUSINESS_ENTITY_SHOP_ID)->willReturn($businessEntityShop);

        return new CustomerB2bValidator(
            $customerRepository,
            $customerB2bRepository,
            $businessEntityRepository,
            $b2bRoleRepository,
            $shopRepository,
        );
    }

    private function buildCommand(): AddCustomerB2bCommand
    {
        return new AddCustomerB2bCommand(42, self::BUSINESS_ENTITY_ID, 3);
    }
}
