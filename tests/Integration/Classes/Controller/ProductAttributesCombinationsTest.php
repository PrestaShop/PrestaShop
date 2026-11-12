<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Configuration;
use Context;
use FilesystemIterator;
use Product;
use ProductController;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Smarty;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * assignAttributesCombinations() is deprecated and no longer called by the controller, but it stays
 * callable so an override that still wants the `attributesCombinations` variable keeps working. This pins
 * that promise, and the shape it hands over.
 */
class ProductAttributesCombinationsTest extends KernelTestCase
{
    private const PRODUCT_WITH_COMBINATIONS = 1;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
    }

    public function testTheDeprecatedMethodStillAssignsItsVariables(): void
    {
        $product = new Product(self::PRODUCT_WITH_COMBINATIONS, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        self::assertTrue($product->hasCombinations(), 'the fixture product must carry combinations');

        $smarty = $this->recordingSmarty();
        $controller = new class() extends ProductController {
            public function __construct()
            {
                // the controller's own constructor initialises the whole front office; this test only
                // exercises one protected method, so it is deliberately skipped
            }

            public function callAssignAttributesCombinations(Product $product, $smarty): array
            {
                $this->product = $product;
                $this->context = Context::getContext();
                $originalSmarty = $this->context->smarty;
                $this->context->smarty = $smarty;

                try {
                    $this->assignAttributesCombinations();
                } finally {
                    $this->context->smarty = $originalSmarty;
                }

                return $smarty->assigned;
            }
        };

        $assigned = $controller->callAssignAttributesCombinations($product, $smarty);

        self::assertArrayHasKey('attributesCombinations', $assigned);
        self::assertIsArray($assigned['attributesCombinations']);
        self::assertNotEmpty($assigned['attributesCombinations'], 'a product with combinations must produce rows');
        self::assertArrayHasKey('attribute', $assigned['attributesCombinations'][0]);
        self::assertArrayHasKey('group', $assigned['attributesCombinations'][0]);
        self::assertSame(
            Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
            $assigned['attribute_anchor_separator']
        );
    }

    /**
     * The premise that makes dropping the call safe: nothing shipped reads the variable. If a consumer is
     * ever added, it will read an undefined variable, and this says so before that ships.
     */
    public function testNothingShippedReadsTheVariable(): void
    {
        $root = _PS_ROOT_DIR_;
        $roots = [$root . '/classes', $root . '/controllers', $root . '/src', $root . '/themes'];
        $consumers = [];

        foreach ($roots as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $files = new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                    static fn (SplFileInfo $file) => 'node_modules' !== $file->getFilename()
                )
            );
            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                if (!$file->isFile() || !in_array($file->getExtension(), ['php', 'tpl'], true)) {
                    continue;
                }
                $path = $file->getPathname();
                if (str_ends_with($path, 'controllers/front/ProductController.php')) {
                    continue; // the deprecated method itself
                }
                if (str_contains((string) file_get_contents($path), 'attributesCombinations')) {
                    $consumers[] = substr($path, strlen($root) + 1);
                }
            }
        }

        self::assertSame([], $consumers, 'the attributesCombinations variable is no longer assigned');
    }

    private function recordingSmarty()
    {
        return new class() extends Smarty {
            /** @var array<string, mixed> */
            public array $assigned = [];

            public function assign($tpl_var, $value = null, $nocache = false)
            {
                if (is_array($tpl_var)) {
                    $this->assigned = array_merge($this->assigned, $tpl_var);
                } else {
                    $this->assigned[$tpl_var] = $value;
                }

                return $this;
            }
        };
    }
}
