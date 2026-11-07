<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use AdminCartRulesController;
use Context;
use ControllerCore;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The two "Compatibility with other cart rules" lists are not filtered, on purpose: compatibility is a
 * lasting relation and a rule disabled today can be enabled tomorrow. That makes the label the only
 * thing a merchant has to tell one rule from another, and a name on its own is not enough - nothing
 * stops two rules sharing one, and an expired rule looks exactly like a live one.
 */
class CartRuleCompatibilityOptionTest extends KernelTestCase
{
    private const DAY = 86400;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @dataProvider getCartRules
     */
    public function testTheOptionSaysWhichRuleItIs(array $cartRule, string $expected, string $because): void
    {
        self::assertSame($expected, strip_tags($this->renderOption($cartRule)), $because);
    }

    public static function getCartRules(): array
    {
        $past = date('Y-m-d H:i:s', time() - self::DAY);
        $future = date('Y-m-d H:i:s', time() + self::DAY);

        return [
            'usable rule with a code' => [
                ['id_cart_rule' => 7, 'name' => 'Spring sale', 'active' => '1', 'code' => 'SPRING', 'date_from' => $past, 'date_to' => $future],
                '&nbsp;#7 - Spring sale - SPRING',
                'a usable rule gets no state suffix',
            ],
            'automatic rule has no code to show' => [
                ['id_cart_rule' => 8, 'name' => 'Free shipping', 'active' => '1', 'code' => '', 'date_from' => $past, 'date_to' => $future],
                '&nbsp;#8 - Free shipping',
                'an empty code must not leave a dangling separator',
            ],
            'disabled rule' => [
                ['id_cart_rule' => 9, 'name' => 'Free shipping', 'active' => '0', 'code' => '', 'date_from' => $past, 'date_to' => $future],
                '&nbsp;#9 - Free shipping (Disabled)',
                'disabled outranks the dates, and this is the case the issue reports',
            ],
            'expired rule' => [
                ['id_cart_rule' => 10, 'name' => 'Free shipping', 'active' => '1', 'code' => '', 'date_from' => date('Y-m-d H:i:s', time() - 2 * self::DAY), 'date_to' => $past],
                '&nbsp;#10 - Free shipping (Expired)',
                'an expired rule is active but unusable',
            ],
            'rule that has not started' => [
                ['id_cart_rule' => 11, 'name' => 'Free shipping', 'active' => '1', 'code' => '', 'date_from' => $future, 'date_to' => date('Y-m-d H:i:s', time() + 2 * self::DAY)],
                '&nbsp;#11 - Free shipping (Scheduled)',
                'a rule that starts later is not usable yet either',
            ],
        ];
    }

    public function testTwoRulesSharingANameStayDistinguishable(): void
    {
        $common = ['name' => 'Free shipping', 'active' => '1', 'code' => '', 'date_from' => null, 'date_to' => null];

        $first = $this->renderOption(['id_cart_rule' => 12] + $common);
        $second = $this->renderOption(['id_cart_rule' => 13] + $common);

        self::assertNotSame($first, $second, 'the whole point of the issue is that identical names must not render identically');
    }

    private function renderOption(array $cartRule): string
    {
        $controller = (new ReflectionClass(AdminCartRulesController::class))->newInstanceWithoutConstructor();

        // The real controller receives its translator from the constructor, which needs a full request.
        $translator = new ReflectionProperty(ControllerCore::class, 'translator');
        $translator->setAccessible(true);
        $translator->setValue($controller, Context::getContext()->getTranslator());

        $method = new ReflectionMethod(AdminCartRulesController::class, 'renderCartRuleOption');
        $method->setAccessible(true);

        return $method->invoke($controller, $cartRule);
    }
}
