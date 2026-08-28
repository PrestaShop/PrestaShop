<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;

/**
 * Recommended base for product row steps: hosts the shared cell helpers and
 * the auto-creation notice. Steps with more dependencies add their own
 * promoted constructor parameters and call parent::__construct($valueParser).
 * Implementing ProductRowStepInterface directly stays supported.
 */
abstract class AbstractProductRowStep implements ProductRowStepInterface
{
    use ProductRowValueTrait;

    public function __construct(
        protected readonly ValueParser $valueParser,
    ) {
    }

    /**
     * The import creates catalog entities the file only NAMES — brands,
     * categories, features and their values (legacy behavior, kept). That is
     * expected rather than wrong, so it is a NOTICE and not a warning: it
     * records what the run added beyond the products themselves.
     *
     * A pausing phase could not help here — by the time wasCreated is true the
     * entity exists, and the database phase never pauses. The resolvers' quiet
     * caches report wasCreated on the FIRST resolution only, so each created
     * entity is announced once per batch rather than once per row.
     */
    protected function autoCreationNotice(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_NOTICE, ImportPhaseDefinition::PHASE_DATABASE, $message, [$rowIndex], $field);
    }
}
