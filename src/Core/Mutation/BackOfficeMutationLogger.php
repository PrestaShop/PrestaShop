<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShopBundle\Entity\MutationAction;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Bridges explicitly recorded BO mutations to the historical activity log.
 * The two writes are best effort and do not share an atomic transaction.
 */
final class BackOfficeMutationLogger
{
    public const REQUEST_ATTRIBUTE = '_back_office_mutation_logging';

    private const TRACKER_FAILED_ATTRIBUTE = '_back_office_mutation_tracker_failed';

    public function __construct(
        private readonly MutationTracker $mutationTracker,
        private readonly EmployeeContext $employeeContext,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $legacyLogger,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * IDs refer to the affected row (the NEW product for duplication).
     * Details contain only technical metadata: source_id, active and bulk.
     *
     * @param array<string, mixed> $details
     *
     * @throws InvalidArgumentException When the caller supplies invalid mutation data
     */
    public function record(string $table, int $id, MutationAction $action, array $details = []): void
    {
        $objectType = match ($table) {
            'product' => 'Product',
            'category' => 'Category',
            default => throw new InvalidArgumentException('Unsupported mutation table.'),
        };
        $this->validateDetails($table, $id, $action, $details);
        $mutationDetails = [] === $details ? '' : json_encode($details, JSON_THROW_ON_ERROR);
        if (strlen($mutationDetails) > 255) {
            throw new InvalidArgumentException('Mutation details exceed 255 characters.');
        }

        $request = $this->requestStack->getMainRequest();
        if (true !== $request?->attributes->get(self::REQUEST_ATTRIBUTE)
            || null === $this->employeeContext->getEmployee()
        ) {
            return;
        }

        // Keep the legacy log independent of Doctrine errors and Monolog handlers.
        try {
            $this->legacyLogger->info($this->getMessage($objectType, $id, $action, $details), [
                'object_type' => $objectType,
                'object_id' => isset($details['source_id']) ? 0 : $id,
                'allow_duplicate' => true,
            ]);
        } catch (Throwable $exception) {
            $this->reportFailure('legacy log', $table, $id, $exception);
        }

        if (true === $request->attributes->get(self::TRACKER_FAILED_ATTRIBUTE)) {
            return;
        }

        try {
            // This is the same default lazy manager injected into MutationTracker.
            // Only Doctrine/the kernel may reset it; never retry on a closed manager.
            if (!$this->entityManager->isOpen()) {
                throw new RuntimeException('The mutation EntityManager is closed; it must be reset before tracking resumes.');
            }
            $this->mutationTracker->addMutationForEmployee($table, $id, $action, $mutationDetails);
        } catch (Throwable $exception) {
            // Suppress retries only in this main request, including its subrequests.
            $request->attributes->set(self::TRACKER_FAILED_ATTRIBUTE, true);
            $this->reportFailure('mutation tracker', $table, $id, $exception);
        }
    }

    /**
     * @param array<string, mixed> $details
     */
    private function validateDetails(string $table, int $id, MutationAction $action, array $details): void
    {
        if ($id <= 0 || array_diff(array_keys($details), ['source_id', 'active', 'bulk'])) {
            throw new InvalidArgumentException('Invalid mutation ID or unsupported metadata.');
        }
        if (array_key_exists('bulk', $details) && !is_bool($details['bulk'])) {
            throw new InvalidArgumentException('The bulk metadata must be a boolean.');
        }
        if (array_key_exists('source_id', $details)
            && ('product' !== $table || MutationAction::CREATE !== $action
                || !is_int($details['source_id']) || $details['source_id'] <= 0 || $details['source_id'] === $id)
        ) {
            throw new InvalidArgumentException('Duplication requires a new product ID and a positive source ID.');
        }
        if (array_key_exists('active', $details)
            && ('product' !== $table || MutationAction::UPDATE !== $action || !is_bool($details['active']))
        ) {
            throw new InvalidArgumentException('Status metadata requires a product update and a boolean status.');
        }
    }

    /**
     * @param array{source_id?: int, active?: bool, bulk?: bool} $details
     */
    private function getMessage(string $objectType, int $id, MutationAction $action, array $details): string
    {
        if (isset($details['source_id'])) {
            $message = '%s duplicated: (from %d to %d).';
            $values = [$objectType, $details['source_id'], $id];
        } elseif (isset($details['active'])) {
            $message = $details['active'] ? '%s activated: %d' : '%s deactivated: %d';
            $values = [$objectType, $id];
        } else {
            $message = match ($action) {
                MutationAction::CREATE => '%s addition',
                MutationAction::UPDATE => '%s modification',
                MutationAction::DELETE => '%s deletion',
            };
            $values = [$objectType];
        }

        return sprintf($this->translator->trans($message, [], 'Admin.Advparameters.Feature'), ...$values);
    }

    private function reportFailure(string $destination, string $table, int $id, Throwable $exception): void
    {
        try {
            $this->logger->error('Unable to record Back Office mutation in ' . $destination . '.', [
                'exception' => $exception,
                'mutation_table' => $table,
                'mutation_row_id' => $id,
            ]);
        } catch (Throwable) {
            // A diagnostic logging failure must not turn a completed BO action into an error.
        }
    }
}
