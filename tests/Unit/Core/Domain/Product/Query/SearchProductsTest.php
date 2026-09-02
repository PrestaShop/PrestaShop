<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Query;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductSearchEmptyPhraseException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;

class SearchProductsTest extends TestCase
{
    public function testEmptyPhrase(): void
    {
        $this->expectException(ProductSearchEmptyPhraseException::class);
        $this->expectExceptionMessage('Product search phrase must be a not empty string');

        new SearchProducts('', 1, 'fr');
    }
}
