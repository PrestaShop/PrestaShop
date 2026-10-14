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
     * @var ImportConfigFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new ImportConfigFactory();
    }

    /**
     * Every name an importable grid links here with must select its entity.
     *
     * @dataProvider provideImportTypeNames
     */
    public function testItSelectsTheEntityNamedInTheQuery(string $importType, int $expectedEntityType): void
    {
        $request = $this->createRequest('GET', ['import_type' => $importType]);

        $this->assertSame($expectedEntityType, $this->factory->buildFromRequest($request)->getEntityType());
    }

    public static function provideImportTypeNames(): array
    {
        return [
            'categories' => ['categories', Entity::TYPE_CATEGORIES],
            'products' => ['products', Entity::TYPE_PRODUCTS],
            'combinations' => ['combinations', Entity::TYPE_COMBINATIONS],
            'customers' => ['customers', Entity::TYPE_CUSTOMERS],
            'addresses' => ['addresses', Entity::TYPE_ADDRESSES],
            'manufacturers' => ['manufacturers', Entity::TYPE_MANUFACTURERS],
            'suppliers' => ['suppliers', Entity::TYPE_SUPPLIERS],
            'alias' => ['alias', Entity::TYPE_ALIAS],
            'contacts' => ['contacts', Entity::TYPE_STORE_CONTACTS],
        ];
    }

    /**
     * The name reaches the factory through the query string, so it cannot be trusted.
     *
     * @dataProvider provideUnusableImportTypes
     */
    public function testItIgnoresAnImportTypeItCannotResolve(string $importType): void
    {
        $request = $this->createRequest('GET', ['import_type' => $importType]);
        $request->getSession()->set('entity', Entity::TYPE_SUPPLIERS);

        $this->assertSame(
            Entity::TYPE_SUPPLIERS,
            $this->factory->buildFromRequest($request)->getEntityType()
        );
    }

    public static function provideUnusableImportTypes(): array
    {
        return [
            'unknown name' => ['not_an_entity'],
            'former slug of the stores grid' => ['stores'],
            'entity name spelled as the grid' => ['attributes'],
            'numeric string' => ['2'],
            'empty' => [''],
        ];
    }

    /**
     * A submitted form wins, otherwise an import_type still present in the URL of a POST
     * would override the entity the user selected in the drop-down.
     */
    public function testASubmittedEntityWinsOverTheQuery(): void
    {
        $request = $this->createRequest(
            'POST',
            ['import_type' => 'categories'],
            ['entity' => (string) Entity::TYPE_CUSTOMERS]
        );

        $this->assertSame(
            Entity::TYPE_CUSTOMERS,
            $this->factory->buildFromRequest($request)->getEntityType()
        );
    }

    public function testItFallsBackToTheSessionWithoutAnImportType(): void
    {
        $request = $this->createRequest('GET');
        $request->getSession()->set('entity', Entity::TYPE_ADDRESSES);

        $this->assertSame(
            Entity::TYPE_ADDRESSES,
            $this->factory->buildFromRequest($request)->getEntityType()
        );
    }

    public function testItDefaultsToCategories(): void
    {
        $request = $this->createRequest('GET');

        $this->assertSame(
            Entity::TYPE_CATEGORIES,
            $this->factory->buildFromRequest($request)->getEntityType()
        );
    }

    private function createRequest(string $method, array $query = [], array $body = []): Request
    {
        $request = Request::create('/configure/advanced/import/', $method, $method === 'POST' ? $body : []);
        $request->query->replace($query);
        $request->setSession(new Session(new MockArraySessionStorage()));

        return $request;
    }
}
