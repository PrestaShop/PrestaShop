<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus;

class ImportJobStatusTest extends TestCase
{
    public function testTerminalStatusesAreTheOnesNothingCanMove(): void
    {
        $this->assertTrue(ImportJobStatus::CANCELLED->isTerminal());
        $this->assertTrue(ImportJobStatus::FAILED->isTerminal());
        $this->assertTrue(ImportJobStatus::FINISHED->isTerminal());

        $this->assertFalse(ImportJobStatus::PENDING->isTerminal());
        $this->assertFalse(ImportJobStatus::RUNNING->isTerminal());
        $this->assertFalse(ImportJobStatus::AWAITING_CONFIRMATION->isTerminal());
    }

    /**
     * Continuing a paused job is how the pause is accepted, so AWAITING_CONFIRMATION must say yes.
     */
    public function testEveryNonTerminalStatusCanBeContinued(): void
    {
        foreach (ImportJobStatus::cases() as $status) {
            $this->assertSame(!$status->isTerminal(), $status->canContinue(), $status->value);
        }

        $this->assertTrue(ImportJobStatus::AWAITING_CONFIRMATION->canContinue());
    }

    public function testStoredValuesRoundTrip(): void
    {
        foreach (ImportJobStatus::cases() as $status) {
            $this->assertSame($status, ImportJobStatus::from($status->value));
        }

        $this->assertNull(ImportJobStatus::tryFrom('in_progress'), 'An unknown stored value must not be guessed at');
    }
}
