<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Customer;
use PHPUnit\Framework\TestCase;
use Product;
use SpecificPriceRule;
use Store;

/**
 * A catalog price rule with no reduction is not a price rule. The validator that says so only runs if
 * ObjectModel lets it run, and ObjectModel skips validation for any value `empty()` calls empty - which
 * covers '0', 0 and 0.0. Both halves are tested here: the reduction has to be rejected in every shape a
 * caller can write it, and the fields of other models that legitimately hold a zero must keep saving.
 */
class SpecificPriceRuleValidationTest extends TestCase
{
    /**
     * @dataProvider provideRejectedReductions
     *
     * @param mixed $reduction
     */
    public function testAReductionOfZeroIsRejectedWhateverTypeItArrivesAs($reduction): void
    {
        $rule = new SpecificPriceRule();

        $this->assertNotTrue(
            $rule->validateField('reduction', $reduction),
            sprintf('a reduction of %s was accepted', var_export($reduction, true))
        );
    }

    public function provideRejectedReductions(): iterable
    {
        // the back office form posts a string; a module, an import or the webservice writes a number
        yield "string '0'" => ['0'];
        yield "string '0.00'" => ['0.00'];
        yield "string '0.0'" => ['0.0'];
        yield 'int 0' => [0];
        yield 'float 0.0' => [0.0];
    }

    /**
     * @dataProvider provideAcceptedReductions
     *
     * @param mixed $reduction
     */
    public function testAReductionAboveZeroIsStillAccepted($reduction): void
    {
        $rule = new SpecificPriceRule();

        $this->assertTrue($rule->validateField('reduction', $reduction));
    }

    public function provideAcceptedReductions(): iterable
    {
        yield "string '5'" => ['5'];
        yield "string '0.01'" => ['0.01'];
        yield 'float 5.0' => [5.0];
        yield 'int 5' => [5];
    }

    /**
     * The regression guard. Widening ObjectModel's "is there a value here" test instead of the rule's own
     * validator reaches every field of every model: a store on the equator, a product with no minimum
     * quantity and a customer whose name is one character all carry a zero that has always been valid.
     *
     * @dataProvider provideZerosThatMustStillBeAccepted
     */
    public function testAZeroThatIsValidElsewhereIsStillAccepted(object $model, string $field, $value): void
    {
        $this->assertTrue(
            $model->validateField($field, $value),
            sprintf('%s->%s rejected %s', get_class($model), $field, var_export($value, true))
        );
    }

    public function provideZerosThatMustStillBeAccepted(): iterable
    {
        yield 'store on the equator' => [new Store(), 'latitude', '0'];
        yield 'store on the prime meridian' => [new Store(), 'longitude', '0'];
        yield 'product with no minimum quantity' => [new Product(), 'minimal_quantity', '0'];
        yield 'customer named 0' => [new Customer(), 'firstname', '0'];
    }
}
