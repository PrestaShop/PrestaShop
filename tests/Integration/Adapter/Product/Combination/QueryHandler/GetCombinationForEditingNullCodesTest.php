<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Combination\QueryHandler;

use Combination;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler\GetCombinationForEditingHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use TypeError;

/**
 * The ean13, isbn, mpn, reference and upc columns of product_attribute are nullable, so a
 * combination created without these codes (import, webservice, raw SQL) carries null values.
 * Building the CombinationDetails query result for such a combination must not crash on the
 * string type hints, otherwise the combination cannot be edited in the Back Office.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/39988
 */
class GetCombinationForEditingNullCodesTest extends TestCase
{
    public function testNullCombinationCodesAreNormalisedToEmptyStrings(): void
    {
        $numberExtractor = new NumberExtractor(PropertyAccess::createPropertyAccessor());

        $handler = (new ReflectionClass(GetCombinationForEditingHandler::class))->newInstanceWithoutConstructor();
        $numberExtractorProperty = new ReflectionProperty($handler, 'numberExtractor');
        $numberExtractorProperty->setAccessible(true);
        $numberExtractorProperty->setValue($handler, $numberExtractor);

        $combination = new Combination();
        $combination->ean13 = null;
        $combination->isbn = null;
        $combination->mpn = null;
        $combination->reference = null;
        $combination->upc = null;
        $combination->weight = 0;

        $getDetails = new ReflectionMethod($handler, 'getDetails');
        $getDetails->setAccessible(true);

        try {
            /** @var CombinationDetails $details */
            $details = $getDetails->invoke($handler, $combination);
        } catch (TypeError $e) {
            $this->fail('Building CombinationDetails with null codes must not throw a TypeError: ' . $e->getMessage());
        }

        $this->assertSame('', $details->getEan13());
        $this->assertSame('', $details->getIsbn());
        $this->assertSame('', $details->getMpn());
        $this->assertSame('', $details->getReference());
        $this->assertSame('', $details->getUpc());
    }
}
