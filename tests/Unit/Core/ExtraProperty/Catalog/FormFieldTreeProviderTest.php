<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalogInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormFieldTreeProvider;
use Psr\Log\NullLogger;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Forms;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\AddressFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\BrokenFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\DeepFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\RootFixtureType;

class FormFieldTreeProviderTest extends TestCase
{
    public function testTreePathsLabelsTypesAndCompound(): void
    {
        $tree = $this->createProvider()->getTree('root_fixture');

        $this->assertNotNull($tree);
        $this->assertCount(3, $tree);

        [$name, $address, $active] = $tree;

        $this->assertSame('name', $name->name);
        $this->assertSame('name', $name->path);
        // Explicit label option is used as is
        $this->assertSame('The name', $name->label);
        $this->assertSame(TextType::class, $name->typeClass);
        $this->assertFalse($name->compound);
        $this->assertSame([], $name->children);

        $this->assertSame('shipping_address', $address->name);
        $this->assertSame('shipping_address', $address->path);
        // No label option: humanized field name
        $this->assertSame('Shipping address', $address->label);
        $this->assertSame(AddressFixtureType::class, $address->typeClass);
        $this->assertTrue($address->compound);
        $this->assertCount(2, $address->children);

        [$street, $city] = $address->children;
        $this->assertSame('shipping_address.street', $street->path);
        $this->assertSame('Street label', $street->label);
        // label === false is not a string: humanized field name
        $this->assertSame('shipping_address.city', $city->path);
        $this->assertSame('City', $city->label);

        $this->assertSame(CheckboxType::class, $active->typeClass);
        $this->assertFalse($active->compound);
    }

    public function testTreeIsCappedAtDepthSix(): void
    {
        $tree = $this->createProvider()->getTree('deep_fixture');

        $this->assertNotNull($tree);
        $this->assertCount(1, $tree);

        $node = $tree[0];
        $depth = 1;
        while ([] !== $node->children) {
            $this->assertCount(1, $node->children);
            $node = $node->children[0];
            ++$depth;
        }

        $this->assertSame(6, $depth);
        $this->assertSame('child.child.child.child.child.child', $node->path);
        // The node is still compound: only its children were pruned by the depth cap
        $this->assertTrue($node->compound);
    }

    public function testUnknownFormIdReturnsNull(): void
    {
        $this->assertNull($this->createProvider()->getTree('unknown_form'));
    }

    public function testUnbuildableFormReturnsNull(): void
    {
        $this->assertNull($this->createProvider()->getTree('broken_fixture'));
    }

    private function createProvider(): FormFieldTreeProvider
    {
        $formCatalog = $this->createMock(FormCatalogInterface::class);
        $formCatalog->method('getFormTypeClass')->willReturnCallback(
            static fn (string $formId): ?string => [
                'root_fixture' => RootFixtureType::class,
                'deep_fixture' => DeepFixtureType::class,
                'broken_fixture' => BrokenFixtureType::class,
            ][$formId] ?? null
        );

        return new FormFieldTreeProvider($formCatalog, Forms::createFormFactory(), new NullLogger());
    }
}
