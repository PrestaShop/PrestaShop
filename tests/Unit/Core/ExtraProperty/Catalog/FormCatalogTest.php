<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalog;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class FormCatalogTest extends TestCase
{
    private const PREFIX_BY_CLASS = [
        'Fake\Form\ProductType' => 'product',
        'Fake\Form\DuplicatedProductType' => 'product',
        'Fake\Form\MySettingsType' => 'my_settings',
    ];

    public function testLabelsReuseGridLabelOrHumanizeAndListIsSorted(): void
    {
        $catalog = $this->createCatalog([
            'Fake\Form\ProductType',
            'Fake\Form\MySettingsType',
        ]);

        $entries = $catalog->getAll();

        $this->assertCount(2, $entries);
        // Sorted by label: "My settings" before "Products list"
        $this->assertSame('my_settings', $entries[0]['id']);
        // No grid with the "my_settings" id: humanized block prefix
        $this->assertSame('My settings', $entries[0]['label']);
        $this->assertSame('product', $entries[1]['id']);
        // A grid with the "product" id exists: its translated label is reused
        $this->assertSame('Products list', $entries[1]['label']);
    }

    public function testBrokenFormTypeIsSkipped(): void
    {
        $catalog = $this->createCatalog([
            'Fake\Form\ProductType',
            'Fake\Form\UnresolvableType',
        ]);

        $entries = $catalog->getAll();

        $this->assertCount(1, $entries);
        $this->assertSame('product', $entries[0]['id']);
    }

    public function testDuplicatedBlockPrefixKeepsFirstFormType(): void
    {
        $catalog = $this->createCatalog([
            'Fake\Form\ProductType',
            'Fake\Form\DuplicatedProductType',
        ]);

        $this->assertCount(1, $catalog->getAll());
        $this->assertSame('Fake\Form\ProductType', $catalog->getFormTypeClass('product'));
    }

    public function testHasAndGetFormTypeClass(): void
    {
        $catalog = $this->createCatalog([
            'Fake\Form\ProductType',
            'Fake\Form\MySettingsType',
        ]);

        $this->assertTrue($catalog->has('product'));
        $this->assertTrue($catalog->has('my_settings'));
        $this->assertFalse($catalog->has('unknown'));

        $this->assertSame('Fake\Form\MySettingsType', $catalog->getFormTypeClass('my_settings'));
        $this->assertNull($catalog->getFormTypeClass('unknown'));
    }

    /**
     * @param list<string> $formTypeClasses
     */
    private function createCatalog(array $formTypeClasses): FormCatalog
    {
        return new FormCatalog(
            $formTypeClasses,
            $this->createRegistry(),
            $this->createGridCatalog(['product' => 'Products list']),
            new NullLogger(),
            new ArrayAdapter(),
        );
    }

    private function createRegistry(): FormRegistryInterface
    {
        $registry = $this->createMock(FormRegistryInterface::class);
        $registry->method('getType')->willReturnCallback(function (string $formTypeClass): ResolvedFormTypeInterface {
            if (!array_key_exists($formTypeClass, self::PREFIX_BY_CLASS)) {
                throw new RuntimeException(sprintf('Could not load type "%s"', $formTypeClass));
            }

            $resolvedType = $this->createMock(ResolvedFormTypeInterface::class);
            $resolvedType->method('getBlockPrefix')->willReturn(self::PREFIX_BY_CLASS[$formTypeClass]);

            return $resolvedType;
        });

        return $registry;
    }

    /**
     * @param array<string, string> $gridLabelsById
     */
    private function createGridCatalog(array $gridLabelsById): GridCatalog
    {
        $gridCatalog = $this->createMock(GridCatalog::class);
        $gridCatalog->method('get')->willReturnCallback(
            static fn (string $gridId): ?array => isset($gridLabelsById[$gridId])
                ? ['id' => $gridId, 'label' => $gridLabelsById[$gridId], 'columns' => []]
                : null
        );

        return $gridCatalog;
    }
}
