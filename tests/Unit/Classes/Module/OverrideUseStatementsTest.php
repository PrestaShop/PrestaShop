<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Module;

use Module;
use PHPUnit\Framework\TestCase;

/**
 * Installing a module override on a class that already has one merges the class body only, so the
 * imports the module declared above its class were dropped and the merged methods referred to short
 * class names nothing imported.
 */
class OverrideUseStatementsTest extends TestCase
{
    public function testAnImportTheOverrideLacksIsAdded(): void
    {
        $result = $this->merge(
            ["<?php\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            ["<?php\n", "\n", "use PrestaShop\\PrestaShop\\Core\\Cart\\CartRuleData;\n", "\n", "class Cart extends CartCore\n"]
        );

        $this->assertSame(
            ["<?php\n", "use PrestaShop\\PrestaShop\\Core\\Cart\\CartRuleData;\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            $result
        );
    }

    public function testAnImportTheOverrideAlreadyHasIsNotDuplicated(): void
    {
        $existing = [
            "<?php\n",
            "use PrestaShop\\PrestaShop\\Core\\Cart\\CartRuleData;\n",
            "\n",
            "class Cart extends CartCore\n",
            "{\n",
            "}\n",
        ];

        $result = $this->merge($existing, ["<?php\n", "use PrestaShop\\PrestaShop\\Core\\Cart\\CartRuleData;\n", "class Cart extends CartCore\n"]);

        $this->assertSame($existing, $result);
    }

    public function testTheImportGoesAfterTheLastExistingOne(): void
    {
        $result = $this->merge(
            ["<?php\n", "use A\\B;\n", "use C\\D;\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            ["<?php\n", "use E\\F;\n", "class Cart extends CartCore\n"]
        );

        $this->assertSame(
            ["<?php\n", "use A\\B;\n", "use C\\D;\n", "use E\\F;\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            $result
        );
    }

    public function testAModuleDeclaringNoImportsChangesNothing(): void
    {
        $existing = ["<?php\n", "class Cart extends CartCore\n", "{\n", "    use SomeTrait;\n", "}\n"];

        $result = $this->merge($existing, ["<?php\n", "class Cart extends CartCore\n"]);

        $this->assertSame($existing, $result);
    }

    /**
     * A class body can contain a trait import, which is not a namespace import. Reading the existing
     * file stops at the class declaration so that one is never mistaken for an import, and a real
     * import is still added above.
     */
    public function testTheImportGoesAfterAStrictTypesDeclaration(): void
    {
        // declare(strict_types=1) has to be the very first statement, so an import placed between the
        // opening tag and it makes the merged override a parse error - and the installer only finds out
        // when it eval()s the class.
        $result = $this->merge(
            ["<?php\n", "\n", "declare(strict_types=1);\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            ["<?php\n", "use A\\B;\n", "class Cart extends CartCore\n"]
        );

        $this->assertSame(
            ["<?php\n", "\n", "declare(strict_types=1);\n", "use A\\B;\n", "\n", "class Cart extends CartCore\n", "{\n", "}\n"],
            $result
        );
        $declareAt = array_search("declare(strict_types=1);\n", $result, true);
        $importAt = array_search("use A\\B;\n", $result, true);
        $this->assertIsInt($declareAt);
        $this->assertIsInt($importAt);
        $this->assertLessThan($importAt, $declareAt, 'the import must not be placed before the declaration');
        foreach (array_slice($result, 1, $declareAt - 1) as $before) {
            $this->assertSame('', trim($before), 'nothing but blank lines may sit between the open tag and the declaration');
        }
    }

    public function testATraitUsedInsideTheClassIsNotMistakenForAnImport(): void
    {
        $result = $this->merge(
            ["<?php\n", "class Cart extends CartCore\n", "{\n", "    use SomeTrait;\n", "}\n"],
            ["<?php\n", "use A\\B;\n", "class Cart extends CartCore\n"]
        );

        $this->assertSame(
            ["<?php\n", "use A\\B;\n", "class Cart extends CartCore\n", "{\n", "    use SomeTrait;\n", "}\n"],
            $result
        );
    }

    /**
     * @param array<int, string> $overrideFile
     * @param array<int, string> $moduleHeader
     *
     * @return array<int, string>
     */
    private function merge(array $overrideFile, array $moduleHeader): array
    {
        $module = new TestableModuleOverride();

        return $module->merge($overrideFile, $moduleHeader);
    }
}

class TestableModuleOverride extends Module
{
    public function __construct()
    {
        // The real constructor boots a module context a unit test has no use for.
    }

    /**
     * @param array<int, string> $overrideFile
     * @param array<int, string> $moduleHeader
     *
     * @return array<int, string>
     */
    public function merge(array $overrideFile, array $moduleHeader): array
    {
        return $this->addMissingUseStatements($overrideFile, $moduleHeader);
    }
}
