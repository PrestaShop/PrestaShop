<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\AttributeGroup\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetAttributeGroupForEditingHandlerTest extends KernelTestCase
{
    /**
     * @var object|null
     */
    private $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');
    }

    /**
     * EditableAttributeGroup must expose the group position, otherwise the Admin API
     * POST /attributes/group response is missing the "position" key. See issue #39729.
     */
    public function testGetAttributeGroupForEditingReturnsThePosition(): void
    {
        $row = Db::getInstance()->getRow('
            SELECT id_attribute_group, position
            FROM ' . _DB_PREFIX_ . 'attribute_group
            ORDER BY position DESC');
        self::assertNotEmpty($row, 'Demo data must contain at least one attribute group.');

        /** @var EditableAttributeGroup $result */
        $result = $this->queryBus->handle(new GetAttributeGroupForEditing((int) $row['id_attribute_group']));

        self::assertSame((int) $row['position'], $result->getPosition());
    }
}
