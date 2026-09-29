<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspRuleValidator;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShopBundle\Entity\CspRule;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/** The cross-row uniqueness rule: a source may be allowed only once per (shop, directive). */
class CspRuleValidatorTest extends TestCase
{
    public function testItThrowsADuplicateRuleErrorWhenTheSourceIsAlreadyAllowed(): void
    {
        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('findOneByShopDirectiveSource')->willReturn(new CspRule());

        try {
            (new CspRuleValidator($repository))->assertSourceIsNotAlreadyAllowed(1, 'script-src', 'https://cdn.example.com');
            $this->fail('Expected a DUPLICATE_RULE constraint exception');
        } catch (CspConstraintException $e) {
            $this->assertSame(CspConstraintException::DUPLICATE_RULE, $e->getCode());
        }
    }

    public function testItPassesWhenTheSourceIsNotYetAllowed(): void
    {
        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('findOneByShopDirectiveSource')->willReturn(null);

        (new CspRuleValidator($repository))->assertSourceIsNotAlreadyAllowed(1, 'script-src', 'https://cdn.example.com');

        $this->expectNotToPerformAssertions();
    }
}
