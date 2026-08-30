<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\AssociationExistenceChecker;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalog;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssociationExistenceCheckerTest extends TestCase
{
    public function testEmptyInputsAreSilent(): void
    {
        $checker = $this->buildChecker();

        $this->assertSame([], $checker->check(null, null, null));
        $this->assertSame([], $checker->check([], [], []));
    }

    public function testKnownTargetsAreSilent(): void
    {
        // Placement modifiers (path/column/methods) must not confuse the id extraction.
        $warnings = $this->buildChecker()->check(
            ['product', 'category:options.seo:after'],
            ['product', 'product:reference:before'],
            ['/products', '/products/{productId}:GET,PATCH']
        );

        $this->assertSame([], $warnings);
    }

    public function testUnknownFormIdWarns(): void
    {
        $warnings = $this->buildChecker()->check(['ghost_form:some.path'], null, null);

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('ghost_form', $warnings[0]);
        $this->assertStringContainsString('No back-office form', $warnings[0]);
    }

    public function testUnknownGridIdWarns(): void
    {
        $warnings = $this->buildChecker()->check(null, ['ghost_grid:reference'], null);

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('ghost_grid', $warnings[0]);
        $this->assertStringContainsString('No back-office grid', $warnings[0]);
    }

    public function testKnownGridWithUnknownColumnWarns(): void
    {
        $warnings = $this->buildChecker()->check(null, ['product:ghost_column:after'], null);

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('product', $warnings[0]);
        $this->assertStringContainsString('ghost_column', $warnings[0]);
        $this->assertStringContainsString('no column', $warnings[0]);
    }

    public function testKnownGridWithoutColumnIsSilent(): void
    {
        $this->assertSame([], $this->buildChecker()->check(null, ['product'], null));
    }

    public function testUnknownApiPathWarns(): void
    {
        // The path is normalized before the catalog lookup (leading slash, no trailing slash).
        $warnings = $this->buildChecker()->check(null, null, ['ghosts/{ghostId}/:GET']);

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('/ghosts/{ghostId}', $warnings[0]);
        $this->assertStringContainsString('No Admin API operation', $warnings[0]);
    }

    public function testOneWarningPerUnknownTarget(): void
    {
        $warnings = $this->buildChecker()->check(
            ['ghost_form', 'product'],
            ['ghost_grid', 'product:ghost_column'],
            ['/ghosts']
        );

        $this->assertCount(4, $warnings);
    }

    private function buildChecker(): AssociationExistenceChecker
    {
        $formCatalog = $this->createMock(FormCatalog::class);
        $formCatalog->method('has')->willReturnCallback(
            static fn (string $formId): bool => in_array($formId, ['product', 'category'], true)
        );

        $gridCatalog = $this->createMock(GridCatalog::class);
        $gridCatalog->method('get')->willReturnCallback(
            static fn (string $gridId): ?array => 'product' === $gridId
                ? [
                    'id' => 'product',
                    'label' => 'Products',
                    'columns' => [
                        ['id' => 'id_product', 'label' => 'ID', 'position' => 0],
                        ['id' => 'reference', 'label' => 'Reference', 'position' => 1],
                    ],
                ]
                : null
        );

        $apiEndpointCatalog = $this->createMock(ApiEndpointCatalog::class);
        $apiEndpointCatalog->method('hasUriTemplate')->willReturnCallback(
            static fn (string $path): bool => in_array($path, ['/products', '/products/{productId}'], true)
        );

        // Identity translator: returns the wording with its placeholders substituted.
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []): string => strtr($id, $parameters)
        );

        return new AssociationExistenceChecker($formCatalog, $gridCatalog, $apiEndpointCatalog, $translator);
    }
}
