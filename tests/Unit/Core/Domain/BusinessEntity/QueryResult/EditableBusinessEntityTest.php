<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class EditableBusinessEntityTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $editable = new EditableBusinessEntity(
            10,
            'Tan Emporium',
            'Tan Emporium SAS',
            'EXT-1',
            true,
            BusinessEntityStatus::ACTIVE,
            5,
            2,
        );

        $this->assertSame(10, $editable->getBusinessEntityId());
        $this->assertSame('Tan Emporium', $editable->getName());
        $this->assertSame('Tan Emporium SAS', $editable->getLegalName());
        $this->assertSame('EXT-1', $editable->getExternalRef());
        $this->assertTrue($editable->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $editable->getStatus());
        $this->assertSame(5, $editable->getCustomerGroupId());
        $this->assertSame(2, $editable->getShopId());
    }

    public function testItAcceptsNullLegalNameAndExternalRef(): void
    {
        $editable = new EditableBusinessEntity(1, 'Name', null, null, false, BusinessEntityStatus::PENDING, 3, 1);

        $this->assertNull($editable->getLegalName());
        $this->assertNull($editable->getExternalRef());
    }
}
