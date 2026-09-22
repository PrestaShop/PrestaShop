<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\ImportJobStatus;

/**
 * The persistence enum only knows what the repository's queries need.
 */
class ImportJobStatusTest extends TestCase
{
    public function testATransitionMayStartFromAnyNonTerminalStatus(): void
    {
        $cases = ImportJobStatus::nonTerminalCases();

        $this->assertTrue(array_is_list($cases), 'The query binds a list, not a keyed array');
        $this->assertSame(
            [ImportJobStatus::AWAITING_CONFIRMATION, ImportJobStatus::PENDING, ImportJobStatus::RUNNING],
            $cases
        );
        foreach ($cases as $case) {
            $this->assertFalse($case->isTerminal(), $case->value);
        }
    }

    public function testItMirrorsTheDomainEnum(): void
    {
        $domainValues = array_map(
            static fn (\PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus $status): string => $status->value,
            \PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus::cases()
        );
        $entityValues = array_map(static fn (ImportJobStatus $status): string => $status->value, ImportJobStatus::cases());

        $this->assertSame($domainValues, $entityValues, 'The adapter converts by value; the two enums must agree');
    }
}
