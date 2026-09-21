<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;

/**
 * Deletes terminal import jobs, and the working files nothing owns any more.
 *
 * A command rather than a console-only routine: the same collection runs from the CLI, from a
 * Behat scenario, from the Admin API and from whatever the back office offers later.
 */
final class PurgeImportJobsCommand
{
    /**
     * @param DateTimeInterface|null $expirationDate anything terminal and untouched since then is
     *                                               collected; null uses the retention window
     *
     * @throws ImportJobConstraintException
     */
    public function __construct(private readonly ?DateTimeInterface $expirationDate = null)
    {
        // a date ahead of now would collect every terminal job, including ones that finished
        // seconds ago — "purge everything" is spelled with the current date, not a future one
        if (null !== $expirationDate && $expirationDate->getTimestamp() > time()) {
            throw new ImportJobConstraintException(
                'The import job expiration date cannot be in the future.',
                ImportJobConstraintException::INVALID_EXPIRATION_DATE
            );
        }
    }

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }
}
