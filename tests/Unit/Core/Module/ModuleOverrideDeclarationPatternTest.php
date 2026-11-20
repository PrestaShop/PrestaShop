<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\ModuleOverrideDeclarationPattern;

class ModuleOverrideDeclarationPatternTest extends TestCase
{
    /**
     * @dataProvider provideMatchingMethods
     */
    public function testMethodPatternCapturesWholeDeclarationHead(string $line, string $name, string $expectedPrefix, string $expectedHead): void
    {
        $this->assertSame(1, preg_match(ModuleOverrideDeclarationPattern::forMethod($name), $line, $matches));
        $this->assertSame($expectedPrefix, $matches[1]);
        $this->assertSame($expectedHead, $matches[2]);
    }

    public static function provideMatchingMethods(): iterable
    {
        yield 'plain' => ['    public function foo()', 'foo', '    ', 'public function foo'];
        yield 'no indentation' => ['public function foo()', 'foo', '', 'public function foo'];
        yield 'static after visibility' => ['    public static function foo(): void', 'foo', '    ', 'public static function foo'];
        yield 'static before visibility' => ['    static public function foo()', 'foo', '    ', 'static public function foo'];
        yield 'final static' => ['    final public static function foo()', 'foo', '    ', 'final public static function foo'];
        yield 'final protected' => ['    final protected function foo()', 'foo', '    ', 'final protected function foo'];
        yield 'abstract' => ['    abstract protected function foo();', 'foo', '    ', 'abstract protected function foo'];
        yield 'return by reference' => ['    private function &foo()', 'foo', '    ', 'private function &foo'];
        yield 'reference with space' => ['    public function & foo()', 'foo', '    ', 'public function & foo'];
        yield 'implicit visibility' => ['    function foo()', 'foo', '    ', 'function foo'];
        yield 'space before parenthesis' => ['    public function foo ()', 'foo', '    ', 'public function foo'];
        yield 'keywords and name are case insensitive' => ['    PUBLIC STATIC FUNCTION Foo()', 'foo', '    ', 'PUBLIC STATIC FUNCTION Foo'];
        yield 'attribute on the same line' => ['    #[Pure] public function foo()', 'foo', '    #[Pure] ', 'public function foo'];
        yield 'tab indentation' => ["\tpublic function foo()", 'foo', "\t", 'public function foo'];
    }

    /**
     * @dataProvider provideNonMatchingMethods
     */
    public function testMethodPatternIgnoresOtherLines(string $line, string $name): void
    {
        $this->assertSame(0, preg_match(ModuleOverrideDeclarationPattern::forMethod($name), $line));
    }

    public static function provideNonMatchingMethods(): iterable
    {
        yield 'name is only a prefix' => ['    public function foobar()', 'foo'];
        yield 'name is only a suffix' => ['    public function barfoo()', 'foo'];
        yield 'method call' => ['    $this->foo();', 'foo'];
        yield 'static call' => ['    return self::foo();', 'foo'];
        yield 'doc block' => ['     * public function foo()', 'foo'];
        yield 'string literal' => ['    $sql = "public function foo(";', 'foo'];
        yield 'property with the same name' => ['    public $foo = 1;', 'foo'];
        yield 'constant with the same name' => ['    public const foo = 1;', 'foo'];
    }

    /**
     * @dataProvider provideMatchingProperties
     */
    public function testPropertyPatternCapturesWholeDeclarationHead(string $line, string $name, string $expectedPrefix, string $expectedHead): void
    {
        $this->assertSame(1, preg_match(ModuleOverrideDeclarationPattern::forProperty($name), $line, $matches));
        $this->assertSame($expectedPrefix, $matches[1]);
        $this->assertSame($expectedHead, $matches[2]);
    }

    public static function provideMatchingProperties(): iterable
    {
        yield 'untyped without default' => ['    public $foo;', 'foo', '    ', 'public $foo'];
        yield 'untyped with default' => ['    protected $foo = 1;', 'foo', '    ', 'protected $foo'];
        yield 'no indentation' => ['public $foo;', 'foo', '', 'public $foo'];
        yield 'static after visibility' => ['    protected static $foo = [];', 'foo', '    ', 'protected static $foo'];
        yield 'static before visibility' => ['    static protected $foo;', 'foo', '    ', 'static protected $foo'];
        yield 'typed' => ['    private string $foo;', 'foo', '    ', 'private string $foo'];
        yield 'nullable typed' => ['    public ?int $foo = null;', 'foo', '    ', 'public ?int $foo'];
        yield 'static nullable typed' => ['    public static ?int $foo = null;', 'foo', '    ', 'public static ?int $foo'];
        yield 'union typed' => ['    public int|string $foo = 1;', 'foo', '    ', 'public int|string $foo'];
        yield 'union with null' => ['    public Foo|null $foo = null;', 'foo', '    ', 'public Foo|null $foo'];
        yield 'intersection typed' => ['    public Foo&Bar $foo;', 'foo', '    ', 'public Foo&Bar $foo'];
        yield 'dnf typed' => ['    public (Foo&Bar)|null $foo = null;', 'foo', '    ', 'public (Foo&Bar)|null $foo'];
        yield 'fully qualified class name' => ['    public ?\DateTimeInterface $foo = null;', 'foo', '    ', 'public ?\DateTimeInterface $foo'];
        yield 'readonly after visibility' => ['    public readonly string $foo;', 'foo', '    ', 'public readonly string $foo'];
        yield 'readonly before visibility' => ['    readonly public string $foo;', 'foo', '    ', 'readonly public string $foo'];
        yield 'final property' => ['    final public int $foo = 1;', 'foo', '    ', 'final public int $foo'];
        yield 'asymmetric visibility' => ['    public private(set) int $foo = 1;', 'foo', '    ', 'public private(set) int $foo'];
        yield 'asymmetric visibility alone' => ['    protected(set) string $foo;', 'foo', '    ', 'protected(set) string $foo'];
        yield 'abstract property' => ['    abstract public string $foo { get; }', 'foo', '    ', 'abstract public string $foo'];
        yield 'property hooks' => ['    public string $foo {', 'foo', '    ', 'public string $foo'];
        yield 'var keyword' => ['    var $foo;', 'foo', '    ', 'var $foo'];
        yield 'multi-line default value' => ['    public static ?array $foo = [', 'foo', '    ', 'public static ?array $foo'];
        yield 'keywords are case insensitive' => ['    PUBLIC STATIC $foo;', 'foo', '    ', 'PUBLIC STATIC $foo'];
        yield 'attribute on the same line' => ['    #[Deprecated] public $foo;', 'foo', '    #[Deprecated] ', 'public $foo'];
        yield 'multiple attributes on the same line' => ['    #[A] #[B("x")] public $foo;', 'foo', '    #[A] #[B("x")] ', 'public $foo'];
        yield 'tab indentation' => ["\tpublic \$foo;", 'foo', "\t", 'public $foo'];
    }

    /**
     * @dataProvider provideNonMatchingProperties
     */
    public function testPropertyPatternIgnoresOtherLines(string $line, string $name): void
    {
        $this->assertSame(0, preg_match(ModuleOverrideDeclarationPattern::forProperty($name), $line));
    }

    public static function provideNonMatchingProperties(): iterable
    {
        yield 'name is only a prefix' => ['    public $foo_bar = 1;', 'foo'];
        yield 'name is only a prefix without separator' => ['    public $foobar;', 'foo'];
        yield 'name is only a suffix' => ['    public $barfoo;', 'foo'];
        yield 'name is case sensitive' => ['    public $Foo;', 'foo'];
        yield 'method with the same name' => ['    public function foo()', 'foo'];
        yield 'constant with the same name' => ['    public const foo = 1;', 'foo'];
        yield 'promoted constructor parameter' => ['        private int $foo,', 'foo'];
        yield 'last promoted constructor parameter' => ['        private int $foo', 'foo'];
        yield 'assignment' => ['    $foo = 1;', 'foo'];
        yield 'assignment on this' => ['    $this->foo = 1;', 'foo'];
        yield 'doc block' => ['     * @var int $foo', 'foo'];
        yield 'string literal' => ["    \$sql = 'public \$foo;';", 'foo'];
        yield 'no modifier at all' => ['    int $foo;', 'foo'];
    }

    /**
     * @dataProvider provideMatchingConstants
     */
    public function testConstantPatternCapturesWholeDeclarationHead(string $line, string $name, string $expectedPrefix, string $expectedHead): void
    {
        $this->assertSame(1, preg_match(ModuleOverrideDeclarationPattern::forConstant($name), $line, $matches));
        $this->assertSame($expectedPrefix, $matches[1]);
        $this->assertSame($expectedHead, $matches[2]);
    }

    public static function provideMatchingConstants(): iterable
    {
        yield 'implicit visibility' => ['    const FOO = 1;', 'FOO', '    ', 'const FOO'];
        yield 'no indentation' => ['const FOO = 1;', 'FOO', '', 'const FOO'];
        yield 'public' => ['    public const FOO = 1;', 'FOO', '    ', 'public const FOO'];
        yield 'private' => ['    private const FOO = [', 'FOO', '    ', 'private const FOO'];
        yield 'final before visibility' => ['    final public const FOO = 1;', 'FOO', '    ', 'final public const FOO'];
        yield 'final after visibility' => ['    public final const FOO = 1;', 'FOO', '    ', 'public final const FOO'];
        yield 'final alone' => ['    final const FOO = 1;', 'FOO', '    ', 'final const FOO'];
        yield 'typed' => ["    public const string FOO = 'x';", 'FOO', '    ', 'public const string FOO'];
        yield 'nullable typed' => ['    protected const ?Foo FOO = null;', 'FOO', '    ', 'protected const ?Foo FOO'];
        yield 'union typed' => ['    public const int|string FOO = 1;', 'FOO', '    ', 'public const int|string FOO'];
        yield 'final typed' => ['    final protected const array FOO = [];', 'FOO', '    ', 'final protected const array FOO'];
        yield 'no space around equal sign' => ['    const FOO=1;', 'FOO', '    ', 'const FOO'];
        yield 'keywords are case insensitive' => ['    PUBLIC CONST FOO = 1;', 'FOO', '    ', 'PUBLIC CONST FOO'];
        yield 'attribute on the same line' => ['    #[Deprecated] public const FOO = 1;', 'FOO', '    #[Deprecated] ', 'public const FOO'];
        yield 'tab indentation' => ["\tconst FOO = 1;", 'FOO', "\t", 'const FOO'];
    }

    /**
     * @dataProvider provideNonMatchingConstants
     */
    public function testConstantPatternIgnoresOtherLines(string $line, string $name): void
    {
        $this->assertSame(0, preg_match(ModuleOverrideDeclarationPattern::forConstant($name), $line));
    }

    public static function provideNonMatchingConstants(): iterable
    {
        yield 'name is only a prefix' => ['    public const FOO_BAR = 1;', 'FOO'];
        yield 'name is only a suffix' => ['    public const BAR_FOO = 1;', 'FOO'];
        yield 'name is case sensitive' => ['    public const foo = 1;', 'FOO'];
        yield 'global constant' => ["    define('FOO', 1);", 'FOO'];
        yield 'constant usage' => ['    return self::FOO;', 'FOO'];
        yield 'doc block' => ['     * const FOO = 1', 'FOO'];
        yield 'property with the same name' => ['    public $FOO = 1;', 'FOO'];
        yield 'method with the same name' => ['    public function FOO()', 'FOO'];
        yield 'string literal' => ["    \$sql = 'const FOO = 1';", 'FOO'];
    }

    /**
     * @dataProvider provideCommentInjection
     */
    public function testCommentIsInjectedInFrontOfTheWholeDeclaration(string $pattern, string $line, string $expected): void
    {
        $this->assertSame($expected, preg_replace($pattern, "$1/* marker */\n    $2", $line));
    }

    public static function provideCommentInjection(): iterable
    {
        yield 'final constant' => [
            ModuleOverrideDeclarationPattern::forConstant('FOO'),
            "    final public const FOO = 1;\n",
            "    /* marker */\n    final public const FOO = 1;\n",
        ];
        yield 'readonly typed property' => [
            ModuleOverrideDeclarationPattern::forProperty('foo'),
            "    protected readonly ?string \$foo = null;\n",
            "    /* marker */\n    protected readonly ?string \$foo = null;\n",
        ];
        yield 'final static method' => [
            ModuleOverrideDeclarationPattern::forMethod('foo'),
            "    final public static function foo(int \$bar): void\n",
            "    /* marker */\n    final public static function foo(int \$bar): void\n",
        ];
        yield 'attribute stays in front of the comment' => [
            ModuleOverrideDeclarationPattern::forMethod('foo'),
            "    #[Pure] public function foo()\n",
            "    #[Pure] /* marker */\n    public function foo()\n",
        ];
        yield 'line without the declaration is left untouched' => [
            ModuleOverrideDeclarationPattern::forMethod('foo'),
            "        \$this->foo();\n",
            "        \$this->foo();\n",
        ];
    }

    /**
     * A class body mixing every supported syntax with lines that must never be taken for a declaration.
     */
    private const SAMPLE_CLASS = <<<'PHP'
<?php
/**
 * public function docBlockMethod() must not be found
 * public $docBlockProperty must not be found
 */
#[SomeAttribute]
class Cart extends CartCore
{
    final public const string TYPED_CONSTANT = 'x';
    private const PLAIN_CONSTANT = [
        'const FAKE_CONSTANT = 1',
    ];
    public static ?int $nullableStatic = null;
    protected readonly string $readonly;
    public private(set) int|string $asymmetric = 1;
    var $legacy;

    #[Pure]
    final public static function finalStatic(): void
    {
        $sql = 'public function fakeMethod()';
        // public $fakeProperty = 1;
        // const FAKE_COMMENTED = 1;
    }

    public function &byReference(array $values, private int $promoted = 0)
    {
        return $values;
    }

    function implicitVisibility()
    {
    }
}
PHP;

    public function testAnyMethodPatternListsEveryMethodOfAFile(): void
    {
        preg_match_all(ModuleOverrideDeclarationPattern::forAnyMethod(), self::SAMPLE_CLASS, $matches);

        $this->assertSame(['finalStatic', 'byReference', 'implicitVisibility'], $matches['name']);
    }

    public function testAnyPropertyPatternListsEveryPropertyOfAFile(): void
    {
        preg_match_all(ModuleOverrideDeclarationPattern::forAnyProperty(), self::SAMPLE_CLASS, $matches);

        $this->assertSame(['nullableStatic', 'readonly', 'asymmetric', 'legacy'], $matches['name']);
    }

    public function testAnyConstantPatternListsEveryConstantOfAFile(): void
    {
        preg_match_all(ModuleOverrideDeclarationPattern::forAnyConstant(), self::SAMPLE_CLASS, $matches);

        $this->assertSame(['TYPED_CONSTANT', 'PLAIN_CONSTANT'], $matches['name']);
    }

    public function testAnyPatternsKeepTheSameGroupsAsNamedPatterns(): void
    {
        $this->assertSame(1, preg_match(ModuleOverrideDeclarationPattern::forAnyConstant(), '    #[A] final public const string FOO = 1;', $matches));
        $this->assertSame('    #[A] ', $matches[1]);
        $this->assertSame('final public const string FOO', $matches[2]);
        $this->assertSame('FOO', $matches['name']);
    }
}
