<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class PdfTemplateResolverTest extends TestCase
{
    private string $projectDir;

    private ?Shop $previousShop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectDir = sys_get_temp_dir() . '/' . uniqid('pdf_template_resolver_', true);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->projectDir . '/src/PrestaShopBundle/Resources/views/Pdf/header.html.twig', 'core {{ title }}');
        $filesystem->dumpFile($this->projectDir . '/module/header.html.twig', 'module {{ title }}');

        // The test theme has no pdf/ directory, so the core template is the default one.
        $theme = $this->createMock(Theme::class);
        $theme->method('getName')->willReturn('fake-theme');
        $shop = $this->createMock(Shop::class);
        $shop->theme = $theme;

        $this->previousShop = Context::getContext()->shop;
        Context::getContext()->shop = $shop;
    }

    protected function tearDown(): void
    {
        Context::getContext()->shop = $this->previousShop;
        (new Filesystem())->remove($this->projectDir);

        parent::tearDown();
    }

    public function testRenderWithoutModuleRendersCoreTemplate(): void
    {
        $resolver = $this->createResolver($this->createMock(HookDispatcherInterface::class));

        $this->assertSame('core Invoice', $resolver->render('header', ['title' => 'Invoice']));
    }

    public function testRenderWithModuleTemplatePathRendersModuleTemplate(): void
    {
        $resolver = $this->createResolver($this->createHookDispatcher(
            'actionPdfTemplatePathModifier',
            'template_path',
            $this->projectDir . '/module/header.html.twig'
        ));

        $this->assertSame('module Invoice', $resolver->render('header', ['title' => 'Invoice']));
    }

    public function testRenderWithMissingModuleTemplatePathRendersCoreTemplate(): void
    {
        $resolver = $this->createResolver($this->createHookDispatcher(
            'actionPdfTemplatePathModifier',
            'template_path',
            $this->projectDir . '/module/missing.html.twig'
        ));

        $this->assertSame('core Invoice', $resolver->render('header', ['title' => 'Invoice']));
    }

    public function testRenderWithModuleVariablesRendersModifiedVariables(): void
    {
        $resolver = $this->createResolver($this->createHookDispatcher(
            'actionPdfTemplateVariablesModifier',
            'variables',
            ['title' => 'Modified']
        ));

        $this->assertSame('core Modified', $resolver->render('header', ['title' => 'Invoice']));
    }

    public function testRenderWithUnknownTemplateReturnsEmptyString(): void
    {
        $resolver = $this->createResolver($this->createMock(HookDispatcherInterface::class));

        $this->assertSame('', $resolver->render('unknown', []));
    }

    private function createResolver(HookDispatcherInterface $hookDispatcher): PdfTemplateResolver
    {
        return new PdfTemplateResolver(new Environment(new ArrayLoader()), $this->projectDir, $hookDispatcher);
    }

    /**
     * Mimics a module setting $parameter of $hookName, passed by reference.
     */
    private function createHookDispatcher(string $hookName, string $parameter, mixed $value): HookDispatcherInterface
    {
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcher->method('dispatchWithParameters')
            ->willReturnCallback(static function (string $dispatchedHookName, array $parameters) use ($hookName, $parameter, $value): void {
                if ($dispatchedHookName === $hookName) {
                    $parameters[$parameter] = $value;
                }
            });

        return $hookDispatcher;
    }
}
