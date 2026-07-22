<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class AddBusinessEntityCommandTest extends TestCase
{
    public const DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF = 'EXT-001';
    public const DEFAULT_BUSINESS_ENTITY_CITY = 'City';
    public const DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING = 'Billing';
    public const DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING = 'Shipping';
    public const DEFAULT_BUSINESS_ENTITY_POSTCODE = '00000';
    public const DEFAULT_BUSINESS_ENTITY_ADDRESS1 = 'a';
    public const DEFAULT_BUSINESS_ENTITY_LEGAL_NAME = 'b';
    public const DEFAULT_BUSINESS_ENTITY_NAME = 'NAME';
    public const DEFAULT_BUSINESS_ENTITY_SHOP_ID = 1;
    public const DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID = 3;

    public function testItWorksWhenBillingAddressIsShippingAddress(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            true,
            [$billingAddress]
        );

        $this->assertTrue(true);
    }

    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );
        $shippingAddress = new BusinessEntityShippingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $command = new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            false,
            [$billingAddress],
            [$shippingAddress]
        );

        $this->assertSame(self::DEFAULT_BUSINESS_ENTITY_NAME, $command->getName());
        $this->assertSame(self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME, $command->getLegalName());
        $this->assertSame(self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF, $command->getExternalRef());
        $this->assertTrue($command->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $command->getStatus());
        $this->assertSame(self::DEFAULT_BUSINESS_ENTITY_SHOP_ID, $command->getShopId());
        $this->assertSame(self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID, $command->getCustomerGroupId());
        $this->assertFalse($command->isBillingAddressAsShippingAddress());
        $this->assertSame([$billingAddress], $command->getBillingAddresses());
        $this->assertSame([$shippingAddress], $command->getShippingAddresses());
    }

    public function testItWorksWithSeparateAddresses(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $shippingAddress = new BusinessEntityShippingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            false,
            [$billingAddress],
            [$shippingAddress]
        );

        $this->assertTrue(true);
    }

    public function testItThrowsExceptionWhenBillingAddressIsMissing(): void
    {
        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MISSING_BILLING_ADDRESS);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            true,
            []
        );
    }

    public function testItThrowsExceptionWhenDefaultBillingAddressIsMissing(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            false,
            null
        );

        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_BILLING_ADDRESS);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            true,
            [$billingAddress]
        );
    }

    public function testItThrowsExceptionWhenShippingAddressIsMissingAndNotBillingAsShipping(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MISSING_SHIPPING_ADDRESS);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            false,
            [$billingAddress],
            []
        );
    }

    public function testItThrowsExceptionWhenDefaultShippingAddressIsMissing(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $shippingAddress = new BusinessEntityShippingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            false,
            null
        );

        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_SHIPPING_ADDRESS);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            false,
            [$billingAddress],
            [$shippingAddress]
        );
    }

    public function testItThrowsExceptionWhenMultipleDefaultBillingAddresses(): void
    {
        $firstDefaultBillingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );
        $secondDefaultBillingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_BILLING_ADDRESSES);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            true,
            [$firstDefaultBillingAddress, $secondDefaultBillingAddress]
        );
    }

    public function testItThrowsExceptionWhenMultipleDefaultShippingAddresses(): void
    {
        $billingAddress = new BusinessEntityBillingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_BILLING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );
        $firstDefaultShippingAddress = new BusinessEntityShippingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );
        $secondDefaultShippingAddress = new BusinessEntityShippingAddress(
            self::DEFAULT_BUSINESS_ENTITY_ALIAS_SHIPPING,
            self::DEFAULT_BUSINESS_ENTITY_ADDRESS1,
            null,
            self::DEFAULT_BUSINESS_ENTITY_CITY,
            self::DEFAULT_BUSINESS_ENTITY_POSTCODE,
            8,
            true,
            null
        );

        $this->expectException(BusinessEntityBillingAddressConstraintException::class);
        $this->expectExceptionCode(BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_SHIPPING_ADDRESSES);

        new AddBusinessEntityCommand(
            self::DEFAULT_BUSINESS_ENTITY_NAME,
            self::DEFAULT_BUSINESS_ENTITY_LEGAL_NAME,
            self::DEFAULT_BUSINESS_ENTITY_EXTERNAL_REF,
            true,
            BusinessEntityStatus::ACTIVE,
            self::DEFAULT_BUSINESS_ENTITY_SHOP_ID,
            self::DEFAULT_BUSINESS_ENTITY_CUSTOMER_GROUP_ID,
            false,
            [$billingAddress],
            [$firstDefaultShippingAddress, $secondDefaultShippingAddress]
        );
    }
}
