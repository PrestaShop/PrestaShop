<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Configuration;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigFactory;
use PrestaShop\PrestaShop\Core\Import\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ImportConfigFactoryTest extends TestCase
{
    /**
     * @dataProvider getEntityTypeData
     */
    public function testEntityType(array $query, array $post, ?int $sessionEntity, int $expectedEntity): void
    {
        $request = new Request($query, $post);
        $session = new Session(new MockArraySessionStorage());
        if (null !== $sessionEntity) {
            $session->set('entity', $sessionEntity);
        }
        $request->setSession($session);

        $this->assertSame($expectedEntity, (new ImportConfigFactory())->buildFromRequest($request)->getEntityType());
    }

    public static function getEntityTypeData(): iterable
    {
        yield 'default' => [[], [], null, Entity::TYPE_CATEGORIES];
        yield 'session' => [[], [], Entity::TYPE_PRODUCTS, Entity::TYPE_PRODUCTS];
        yield 'import type preselects the entity' => [['import_type' => 'alias'], [], Entity::TYPE_PRODUCTS, Entity::TYPE_ALIAS];
        yield 'unknown import type falls back to session' => [['import_type' => 'features'], [], Entity::TYPE_PRODUCTS, Entity::TYPE_PRODUCTS];
        yield 'submitted entity wins' => [['import_type' => 'alias'], ['entity' => (string) Entity::TYPE_CUSTOMERS], Entity::TYPE_PRODUCTS, Entity::TYPE_CUSTOMERS];
    }
}
