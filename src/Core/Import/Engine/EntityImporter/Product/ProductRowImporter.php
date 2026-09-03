<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\FoundEntity;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ProductFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step\ProductRowStepInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Database-phase import of one mapped product row: update-vs-create decision,
 * then the row steps in tag-priority order — one step per concern, each
 * dispatching its own commands (see ProductRowStepInterface). There is no
 * wrapping transaction (legacy parity): when a command fails mid-row the row
 * is reported as an error, the remaining steps are skipped and the row is
 * marked as skipped for the later phases. A throwing step's own earlier
 * messages are lost with it (steps report by returning); messages from the
 * steps that completed before it survive.
 *
 * Localized values follow the single-language-file rule: on creation the
 * value is duplicated into every installed language, on update only the
 * file's language is written.
 */
class ProductRowImporter
{
    use LocalizedValueTrait;
    use ProductIdentityMessagesTrait;
    use ProductRowValueTrait;

    /**
     * Tag collecting the row steps, applied automatically to any autoconfigured
     * service implementing ProductRowStepInterface (registerForAutoconfiguration
     * in PrestaShopExtension) — module services included, as long as their
     * definitions enable autoconfiguration.
     */
    public const STEP_TAG = 'core.import.product_row_step';

    /**
     * @param iterable<ProductRowStepInterface> $steps tagged iterator, priority-sorted (highest first)
     */
    public function __construct(
        protected readonly iterable $steps,
        protected readonly CommandBusInterface $commandBus,
        protected readonly ValueParser $valueParser,
        protected readonly ProductFinder $productFinder,
        protected readonly ProductRepository $productRepository,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly Tools $tools,
        protected readonly TranslatorInterface $translator,
        protected readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, string> $row mapped row values
     *
     * @return list<ImportMessage> an ERROR severity means the row failed and must be skipped by later phases
     */
    public function importRow(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $messages = [];

        try {
            $languageId = $this->getLanguageId($context);
            $match = $this->productFinder->findRowMatch($row, $context);

            // database-phase defense (validation normally already skipped these
            // rows, but the DB may have changed between phases): the finder only
            // reports the identity problems as data — failing the row is THIS
            // caller's policy, with the same wording as the validator
            $reference = $row['reference'] ?? '';
            if ($match->foundOutsideShopScope) {
                $messages[] = $this->referenceOutsideShopScopeMessage($reference, $rowIndex, ImportPhaseDefinition::PHASE_DATABASE);

                return $messages;
            }
            if ($match->isAmbiguous()) {
                $messages[] = $this->ambiguousReferenceMessage($reference, $match->count(), $rowIndex, ImportPhaseDefinition::PHASE_DATABASE);

                return $messages;
            }

            $isCreation = null === $match->first();

            $productId = $this->resolveTargetProduct($row, $match, $context);

            foreach ($this->steps as $step) {
                if (!$step->supports($row)) {
                    continue;
                }
                $messages = array_merge($messages, $step->apply($row, $rowIndex, $productId, $isCreation, $languageId, $context));
            }
        } catch (Throwable $e) {
            // deliberate catch-all: a failing command must fail THIS ROW only
            // (structured error, remaining commands skipped), never the batch.
            // The full throwable ALWAYS goes to the log; only domain exception
            // messages reach the user (see buildRowFailureMessage()).
            $this->logger->error('Import: product row could not be fully imported', ['row' => $rowIndex, 'exception' => $e]);
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_ERROR,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->buildRowFailureMessage($e),
                [$rowIndex]
            );
        }

        return $messages;
    }

    /**
     * Domain and import-engine exceptions carry a message written for a human
     * (a violated business rule, an ambiguous reference...), so it is worth
     * showing. Anything else — DBAL errors, TypeError... — carries implementation
     * detail such as table and constraint names, which has no place in a
     * back-office notification and is already in the log in full.
     */
    protected function buildRowFailureMessage(Throwable $e): string
    {
        if ($e instanceof DomainException || $e instanceof ImportEngineException) {
            return $this->translator->trans('The row could not be fully imported: %error%', ['%error%' => $e->getMessage()], 'Admin.Advparameters.Notification');
        }

        return $this->translator->trans('The row could not be fully imported because of an unexpected error; see the logs for details.', [], 'Admin.Advparameters.Notification');
    }

    /**
     * @param array<string, string> $row
     */
    protected function resolveTargetProduct(array $row, FoundEntity $match, ImportRunContext $context): int
    {
        if (null !== $match->first()) {
            return $match->first();
        }

        $productType = $this->isVirtual($row) ? ProductType::TYPE_VIRTUAL : ProductType::TYPE_STANDARD;
        $localizedNames = $this->localizeForCreation($row['name'] ?? '');

        if (null !== $match->forcedId) {
            $localizedLinkRewrites = array_map(fn (string $name): string => (string) $this->tools->linkRewrite($name), $localizedNames);
            $this->productRepository->createWithForcedId($match->forcedId, $localizedNames, $localizedLinkRewrites, $productType, $context->getShopId());

            return $match->forcedId;
        }

        return $this->commandBus->handle(
            new AddProductCommand($productType, $context->getShopId(), $localizedNames)
        )->getValue();
    }
}
