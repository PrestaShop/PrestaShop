<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminTaxRulesGroupController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * The "Delete" bulk action on the tax rules of a tax rules group must not crash when it is
 * triggered with no rule selected: tax_ruleBox is then absent from the request and
 * Tools::getValue() returns false, which used to be passed straight into
 * AdminTaxRulesGroupController::deleteTaxRule(array $id_tax_rule_list) and threw a TypeError.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/31351
 */
class AdminTaxRulesGroupBulkDeleteTest extends TestCase
{
    public function testBulkDeleteWithoutSelectionDoesNotThrowTypeError(): void
    {
        // No tax rule checkbox submitted: Tools::getValue('tax_ruleBox') returns false.
        unset($_POST['tax_ruleBox'], $_GET['tax_ruleBox']);

        $controller = (new ReflectionClass(AdminTaxRulesGroupController::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod($controller, 'processBulkDeleteTaxRules');
        $method->setAccessible(true);

        // Before the fix this threw a TypeError (false passed to deleteTaxRule(array)); any throw
        // here fails the test, so the call itself is the assertion. With nothing selected the
        // handler now returns early without touching deleteTaxRule().
        $method->invoke($controller);

        $this->addToAssertionCount(1);
    }
}
