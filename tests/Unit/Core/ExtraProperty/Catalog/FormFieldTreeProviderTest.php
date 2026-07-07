<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormFieldTreeProvider;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Form\Forms;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\BrokenFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\DeepFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\RequiredOptionsFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\RootFixtureType;

class FormFieldTreeProviderTest extends TestCase
{
    public function testTreePathsLabelsAndCompound(): void
    {
        $tree = $this->createProvider()->getTree('root_fixture');

        $this->assertNotNull($tree);
        $this->assertCount(4, $tree);

        [$name, $address, $active, $groups] = $tree;

        $this->assertSame('name', $name['name']);
        $this->assertSame('name', $name['path']);
        // Explicit label option is used as is
        $this->assertSame('The name', $name['label']);
        // The field FQCN is a server-side detail the payload must not leak
        $this->assertArrayNotHasKey('typeClass', $name);
        $this->assertFalse($name['compound']);
        $this->assertSame([], $name['children']);

        $this->assertSame('shipping_address', $address['name']);
        $this->assertSame('shipping_address', $address['path']);
        // No label option: humanized field name
        $this->assertSame('Shipping address', $address['label']);
        $this->assertTrue($address['compound']);
        $this->assertCount(2, $address['children']);

        [$street, $city] = $address['children'];
        $this->assertSame('shipping_address.street', $street['path']);
        $this->assertSame('Street label', $street['label']);
        // label === false is not a string: humanized field name
        $this->assertSame('shipping_address.city', $city['path']);
        $this->assertSame('City', $city['label']);

        $this->assertFalse($active['compound']);

        // An expanded choice is compound on the builder (one child per option), but its children
        // are internal machinery, not placeable fields: it reads as a leaf. The same rule stops
        // the recursion on anything resolving to ChoiceType / CollectionType / Translatable*
        // through its parent chain (e.g. MaterialChoiceTableType, per-language inputs).
        $this->assertSame('groups', $groups['name']);
        $this->assertFalse($groups['compound']);
        $this->assertSame([], $groups['children']);
    }

    public function testTreeIsCappedAtDepthSix(): void
    {
        $tree = $this->createProvider()->getTree('deep_fixture');

        $this->assertNotNull($tree);
        $this->assertCount(1, $tree);

        $node = $tree[0];
        $depth = 1;
        while ([] !== $node['children']) {
            $this->assertCount(1, $node['children']);
            $node = $node['children'][0];
            ++$depth;
        }

        $this->assertSame(6, $depth);
        $this->assertSame('child.child.child.child.child.child', $node['path']);
        // The node is still compound: only its children were pruned by the depth cap
        $this->assertTrue($node['compound']);
    }

    public function testUnknownFormIdReturnsNull(): void
    {
        $this->assertNull($this->createProvider()->getTree('unknown_form'));
    }

    public function testUnbuildableFormReturnsNull(): void
    {
        $this->assertNull($this->createProvider()->getTree('broken_fixture'));
    }

    public function testFormWithRequiredOptionsIsNotIntrospectable(): void
    {
        // No sample options are known for this type (only the product edit form has an inline
        // exception, see FormFieldTreeProvider::introspectionOptions()): the build fails and
        // the form gracefully reads as not introspectable.
        $this->assertNull($this->createProvider()->getTree('required_options_fixture'));
    }

    private function createProvider(): FormFieldTreeProvider
    {
        $formCatalog = $this->createMock(FormCatalog::class);
        $formCatalog->method('getFormTypeClass')->willReturnCallback(
            static fn (string $formId): ?string => [
                'root_fixture' => RootFixtureType::class,
                'deep_fixture' => DeepFixtureType::class,
                'broken_fixture' => BrokenFixtureType::class,
                'required_options_fixture' => RequiredOptionsFixtureType::class,
            ][$formId] ?? null
        );

        return new FormFieldTreeProvider(
            $formCatalog,
            Forms::createFormFactory(),
            new NullLogger(),
            new ArrayAdapter(),
            $this->createMock(ShopContext::class),
            $this->createMock(Connection::class),
            'ps_',
        );
    }
}
