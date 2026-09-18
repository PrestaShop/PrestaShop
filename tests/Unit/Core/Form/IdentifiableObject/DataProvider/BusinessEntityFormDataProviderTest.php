<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\BusinessEntityFormDataProvider;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;

/**
 * AC3: every editable field must reach the form pre-filled with the stored value.
 */
class BusinessEntityFormDataProviderTest extends TestCase
{
    private const BUSINESS_ENTITY_ID = 7;
    private const SHOP_ID = 1;
    private const SHOP_COUNTRY_ID = 8;

    public function testItPreFillsEveryEditableFieldFromTheStoredEntity(): void
    {
        $data = $this->buildProvider($this->buildEditableBusinessEntity())->getData(self::BUSINESS_ENTITY_ID);

        $generalInformation = $data[BusinessEntityType::GENERAL_INFORMATION];

        $this->assertSame('Stored Entity', $generalInformation[BusinessEntityGeneralInformationType::FIELD_NAME]);
        $this->assertSame('Stored Legal', $generalInformation[BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME]);
        $this->assertSame('EXT-010', $generalInformation[BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF]);
        $this->assertTrue($generalInformation[BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED]);
        $this->assertSame(BusinessEntityStatus::ACTIVE, $generalInformation[BusinessEntityGeneralInformationType::FIELD_STATUS]);
        $this->assertSame(9, $generalInformation[BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID]);
        $this->assertSame(self::SHOP_ID, $data[BusinessEntityType::SHOP_ID]);
    }

    public function testItQueriesTheEntityBeingEdited(): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (GetBusinessEntityForEditing $query): bool {
                return self::BUSINESS_ENTITY_ID === $query->getBusinessEntityId()->getValue();
            }))
            ->willReturn($this->buildEditableBusinessEntity());

        $this->buildProvider(null, $queryBus)->getData(self::BUSINESS_ENTITY_ID);
    }

    /**
     * Nullable columns are exposed to the form as empty strings. Nothing is lost on the way back:
     * Symfony normalises the empty optional field to NULL when the form is submitted.
     */
    public function testItExposesNullableFieldsAsEmptyStrings(): void
    {
        $editableBusinessEntity = new EditableBusinessEntity(
            self::BUSINESS_ENTITY_ID,
            'Stored Entity',
            null,
            null,
            false,
            BusinessEntityStatus::PENDING,
            3,
            self::SHOP_ID
        );

        $generalInformation = $this->buildProvider($editableBusinessEntity)
            ->getData(self::BUSINESS_ENTITY_ID)[BusinessEntityType::GENERAL_INFORMATION];

        $this->assertSame('', $generalInformation[BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME]);
        $this->assertSame('', $generalInformation[BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF]);
    }

    public function testDefaultDataStartsAPendingEntityWithTheShopCountry(): void
    {
        $data = $this->buildProvider($this->buildEditableBusinessEntity())->getDefaultData();

        $generalInformation = $data[BusinessEntityType::GENERAL_INFORMATION];

        $this->assertSame('', $generalInformation[BusinessEntityGeneralInformationType::FIELD_NAME]);
        $this->assertFalse($generalInformation[BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED]);
        $this->assertSame(BusinessEntityStatus::PENDING, $generalInformation[BusinessEntityGeneralInformationType::FIELD_STATUS]);

        // The shop country the test name promises: it seeds the first billing address so the State
        // choices can be built. Dropping it used to leave this test green.
        $this->assertSame(
            self::SHOP_COUNTRY_ID,
            $data[BusinessEntityType::BILLING_ADDRESS_TYPE][BusinessEntityFormDataProvider::DEFAULT_BILLING_ADDRESS_INDEX][BusinessEntityAddressType::FIELD_COUNTRY_ID]
        );
    }

    private function buildEditableBusinessEntity(): EditableBusinessEntity
    {
        return new EditableBusinessEntity(
            self::BUSINESS_ENTITY_ID,
            'Stored Entity',
            'Stored Legal',
            'EXT-010',
            true,
            BusinessEntityStatus::ACTIVE,
            9,
            self::SHOP_ID
        );
    }

    private function buildProvider(?EditableBusinessEntity $editableBusinessEntity, ?CommandBusInterface $queryBus = null): BusinessEntityFormDataProvider
    {
        if (null === $queryBus) {
            $queryBus = $this->createMock(CommandBusInterface::class);
            $queryBus->method('handle')->willReturn($editableBusinessEntity);
        }

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getInt')->with('PS_COUNTRY_DEFAULT')->willReturn(self::SHOP_COUNTRY_ID);

        return new BusinessEntityFormDataProvider($configuration, $this->createMock(ShopContext::class), $queryBus);
    }
}
