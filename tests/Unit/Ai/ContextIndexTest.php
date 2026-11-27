<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Ai;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../.ai/bin/context-index.php';

/**
 * The path mapping shared by .ai/bin/check-conventions.php and .ai/bin/generate-context-rules.php.
 */
class ContextIndexTest extends TestCase
{
    private const DOCS = [
        '.ai/Domain/Product/CONTEXT.md' => <<<'MD'
            # Product

            ## Layers

            | Layer | Path |
            |-------|------|
            | Legacy | `classes/Product.php` |
            | Controller | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/` |
            | Shared folder | `src/Core/Form/IdentifiableObject/` |
            | Class name only | `ProductRepository` |

            ## Related

            - [Grid](../../Component/Grid/CONTEXT.md), [ADR](https://github.com/PrestaShop/ADR), [below](#related)
            MD,
        '.ai/Component/Grid/CONTEXT.md' => <<<'MD'
            # Grid

            ## Layers

            | Layer | Path |
            |-------|------|
            | Core | `src/Core/Grid/{Definition,Query}/`, `src/Core/Grid/Query/AbstractDoctrineQueryBuilder.php` |
            | Anywhere | `…/Form/Admin/Grid/` |
            MD,
        '.ai/Component/AdminAPI/CONTEXT.md' => "# Admin API\n\n## Purpose\n\nNo Layers table.\n",
        '.ai/Component/Cookie/CONTEXT.md' => "# Cookie\n\n## Purpose\n\nNo Layers table.\n",
        '.ai/rules/admin-api.md' => <<<'MD'
            ---
            description: "Admin API"
            paths:
              - "src/PrestaShopBundle/ApiPlatform/**"
              - "!src/Legacy/**"
            ---

            Read `.ai/Component/AdminAPI/CONTEXT.md` and `modules/ps_apiresources/.ai/CONTEXT.md`.
            MD,
    ];

    public function testContextGlobs(): void
    {
        $globs = contextGlobs(self::DOCS);

        $this->assertSame(['Component/AdminAPI', 'Component/Cookie', 'Component/Grid', 'Domain/Product'], array_keys($globs));
        $this->assertSame([
            'classes/Product.php',
            'src/Adapter/Product/**',
            'src/Core/Domain/Product/**',
            'src/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/**',
            'tests/Integration/Adapter/Product/**',
            'tests/Integration/Core/Domain/Product/**',
            'tests/Integration/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/**',
            'tests/Unit/Adapter/Product/**',
            'tests/Unit/Core/Domain/Product/**',
            'tests/Unit/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/**',
        ], $globs['Domain/Product'], 'a domain owns its namespaces, not a shared folder without its name');
        $this->assertSame([
            '**/Form/Admin/Grid/**',
            '**/Grid/**',
            'src/Core/Grid/Definition/**',
            'src/Core/Grid/Query/**',
            'tests/Integration/Core/Grid/Definition/**',
            'tests/Integration/Core/Grid/Query/**',
            'tests/Unit/Core/Grid/Definition/**',
            'tests/Unit/Core/Grid/Query/**',
        ], $globs['Component/Grid'], 'braces expanded, …/ at any depth, EXTRA_GLOBS added, covered globs dropped');
        $this->assertSame(['!src/Legacy/**', 'src/PrestaShopBundle/ApiPlatform/**'], $globs['Component/AdminAPI'], 'the paths of the hand-written rule naming it');
        $this->assertSame([], $globs['Component/Cookie']);
    }

    public function testTokenToGlobs(): void
    {
        $this->assertSame(['src/Core/Grid/**'], tokenToGlobs('src/Core/Grid/'));
        $this->assertSame(['src/Core/Grid/Grid.php'], tokenToGlobs('src/Core/Grid/Grid.php'));
        $this->assertSame(['**/Form/Admin/Type/**'], tokenToGlobs('…/Form/Admin/Type/'));
        $this->assertSame(['src/Core/Domain/*/Command/**'], tokenToGlobs('src/Core/Domain/{Domain}/Command/'));
        $this->assertSame(['src/Core/Grid/*Grid', 'src/Core/Grid/*Grid/**'], tokenToGlobs('src/Core/Grid/*Grid'));
    }

    public function testGlobToRegex(): void
    {
        $this->assertMatchesRegularExpression(globToRegex('**/Grid/**'), 'src/Core/Grid/Grid.php');
        $this->assertMatchesRegularExpression(globToRegex('**/Grid/**'), 'Grid/Grid.php');
        $this->assertMatchesRegularExpression(globToRegex('**/Form/**/*Type.php'), 'src/PrestaShopBundle/Form/ProductType.php');
        $this->assertDoesNotMatchRegularExpression(globToRegex('src/Core/*/Grid.php'), 'src/Core/Grid/Sub/Grid.php');
    }

    public function testRuleName(): void
    {
        $this->assertSame('admin-api', ruleName('Component/AdminAPI'));
        $this->assertSame('cqrs', ruleName('Component/CQRS'));
        $this->assertSame('tiny-mce', ruleName('Component/TinyMCE'));
        $this->assertSame('cms-page-category', ruleName('Domain/CmsPageCategory'));
    }

    public function testContextRules(): void
    {
        [$rules, $warnings] = contextRules(self::DOCS);

        $this->assertSame([
            '.claude/rules/component/admin-api.md',
            '.claude/rules/component/grid.md',
            '.claude/rules/domain/product.md',
        ], array_keys($rules), 'no rule for a context without paths');
        $this->assertSame([
            'Component/AdminAPI: glob "!src/Legacy/**" left out, a rule cannot use it',
            'Component/Cookie: no path in its Layers table nor in EXTRA_GLOBS, no rule written',
        ], $warnings);

        $product = $rules['.claude/rules/domain/product.md'];
        $this->assertStringStartsWith("---\npaths:\n  - \"classes/Product.php\"\n", $product);
        $this->assertStringEndsWith(
            "---\n<!-- Generated from .ai/Domain/Product/CONTEXT.md by .ai/bin/generate-ai-index.sh: to change the files it covers, edit the Layers table of the context, not this file -->\n\n"
            . "Read [`.ai/Domain/Product/CONTEXT.md`](../../../.ai/Domain/Product/CONTEXT.md) before changing these files: it holds the conventions of the Product domain.\n",
            $product,
            'a link to the context, never its content'
        );
        $this->assertStringNotContainsString('!src/Legacy', $rules['.claude/rules/component/admin-api.md']);
    }

    public function testCodeRabbitPatterns(): void
    {
        $docs = self::DOCS + ['.ai/Component/Grid/LISTING.md' => "# Listing\n"];
        $docs['.ai/rules/admin-api.md'] .= "\nThen `modules/ps_apiresources/.ai/CONTEXT.md`.\n";

        $yaml = codeRabbitPatterns($docs);

        $this->assertStringContainsString(
            "      - files: \".ai/Component/Grid/CONTEXT.md, .ai/Component/Grid/LISTING.md\"\n        applyTo: \"**/Form/Admin/Grid/**, **/Grid/**, src/Core/Grid/Definition/**,",
            $yaml,
            'every markdown file of the context, on the globs of its rule'
        );
        $this->assertStringContainsString(
            "      - files: \"PrestaShop/ps_apiresources:.ai/CONTEXT.md\"\n        applyTo: \"src/PrestaShopBundle/ApiPlatform/**\"\n",
            $yaml,
            'the module files a hand-written rule names, on the usable globs of the context it targets'
        );
        $this->assertStringNotContainsString('Cookie', $yaml, 'no entry for a context without paths');
    }

    public function testWithCodeRabbitPatterns(): void
    {
        $config = "knowledge_base:\n  code_guidelines:\n    filePatterns:\n      - files: \".ai/CONTEXT.md\"\n        applyTo: \"**\"\n"
            . "      # BEGIN generated by .ai/bin/generate-ai-index.sh\n      - files: \"old\"\n      # END generated\n  learnings: {}\n";

        $this->assertSame(
            str_replace("      - files: \"old\"\n", "      - files: \"new\"\n", $config),
            withCodeRabbitPatterns($config, "      - files: \"new\"\n"),
            'only the lines between the markers change'
        );
        $this->assertNull(withCodeRabbitPatterns("knowledge_base: {}\n", ''), 'no markers, nothing written');
    }
}
