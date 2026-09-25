<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\BusinessEntityFormDataHandler;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;

/**
 * The edit half of AC5: the mapping from submitted form data to the Edit command.
 */
class BusinessEntityFormDataHandlerTest extends TestCase
{
    public function testItMapsTheSubmittedGeneralInformationOntoTheEditCommand(): void
    {
        $command = $this->handleUpdate([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Edited Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Edited Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => 'EXT-010-B',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => true,
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::ACTIVE,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => '9',
        ]);

        $this->assertSame(7, $command->getBusinessEntityId()->getValue());
        $this->assertSame('Edited Entity', $command->getName());
        $this->assertSame('Edited Legal', $command->getLegalName());
        $this->assertSame('EXT-010-B', $command->getExternalRef());
        $this->assertTrue($command->getDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $command->getStatus());
        $this->assertSame(9, $command->getCustomerGroupId());
    }

    /**
     * Boundary guard, not a bug fix: on the form path Symfony already normalises the empty optional
     * field to NULL (pinned by BusinessEntityGeneralInformationTypeTest), so this only covers
     * non-form callers such as the API or an import handing over an empty string.
     */
    public function testItSendsAnEmptiedExternalRefAsNull(): void
    {
        $command = $this->handleUpdate([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Edited Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Edited Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => false,
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => 3,
        ]);

        $this->assertNull($command->getExternalRef());
    }

    public function testItForwardsAnAlreadyNullExternalRefUntouched(): void
    {
        $command = $this->handleUpdate([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Edited Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Edited Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => null,
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => false,
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => 3,
        ]);

        $this->assertNull($command->getExternalRef());
    }

    /**
     * @param array<string, mixed> $generalInformationData
     */
    private function handleUpdate(array $generalInformationData): EditBusinessEntityCommand
    {
        $dispatched = null;

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function ($command) use (&$dispatched) {
                $dispatched = $command;

                return null;
            });

        $handler = new BusinessEntityFormDataHandler($commandBus, $this->createMock(ShopContext::class));
        $handler->update(7, [BusinessEntityType::GENERAL_INFORMATION => $generalInformationData]);

        $this->assertInstanceOf(EditBusinessEntityCommand::class, $dispatched);

        return $dispatched;
    }
}
