<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\IdentifierForViewing;

class IdentifierForViewingTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $identifier = new IdentifierForViewing(5, 'SIREN/SIRET', '123 456 789 00012');

        $this->assertSame(5, $identifier->getBusinessIdentifierId());
        $this->assertSame('SIREN/SIRET', $identifier->getLabel());
        $this->assertSame('123 456 789 00012', $identifier->getValue());
    }

    public function testItAcceptsNullableValue(): void
    {
        $identifier = new IdentifierForViewing(6, 'DUNS number', null);

        $this->assertSame('DUNS number', $identifier->getLabel());
        $this->assertNull($identifier->getValue());
    }
}
