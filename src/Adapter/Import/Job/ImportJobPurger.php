<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use DateTimeImmutable;
use DateTimeInterface;
use DirectoryIterator;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobPurgeSummary;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Collects finished jobs and the working files nothing owns any more.
 *
 * Rows go first and files second, so a file is judged an orphan against a table the purge has
 * already pruned. That also collects the file of a job whose row never existed — a crash between
 * normalizing and persisting — which tracking the deleted rows would have missed.
 */
final class ImportJobPurger
{
    public const RETENTION_DAYS = 7;

    public function __construct(
        private readonly ImportJobRepository $importJobRepository,
        private readonly ImportDirectory $importDirectory,
        private readonly Filesystem $filesystem,
    ) {
    }

    /**
     * @param DateTimeInterface|null $expirationDate anything terminal and untouched since then is
     *                                               collected; defaults to the retention window
     */
    public function purge(?DateTimeInterface $expirationDate = null): ImportJobPurgeSummary
    {
        $expirationDate ??= (new DateTimeImmutable())->modify(sprintf('-%d days', self::RETENTION_DAYS));

        return new ImportJobPurgeSummary(
            $this->importJobRepository->purgeTerminalOlderThan($expirationDate),
            $this->removeOrphanWorkingFiles($expirationDate)
        );
    }

    /**
     * The Start handler writes the working file before persisting its row, so a file younger than
     * the retention may belong to a job being created right now and is never touched.
     */
    private function removeOrphanWorkingFiles(DateTimeInterface $expirationDate): int
    {
        $workingDir = $this->importDirectory->getWorkingDir();
        if (!is_dir($workingDir)) {
            return 0;
        }

        $removed = 0;
        foreach (new DirectoryIterator($workingDir) as $file) {
            if (!$file->isFile() || 'csv' !== strtolower($file->getExtension())) {
                continue;
            }
            if ($file->getMTime() >= $expirationDate->getTimestamp()) {
                continue;
            }
            if (null !== $this->importJobRepository->findByUuid($file->getBasename('.csv'))) {
                continue;
            }

            $this->filesystem->remove($file->getPathname());
            ++$removed;
        }

        return $removed;
    }
}
