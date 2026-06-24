<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import;

use AdminImportController;
use Controller;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ImportCsvFromFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Result\ImportResult;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use ReflectionClass;
use ReflectionProperty;

/**
 * Drives the legacy AdminImportController for the entity types that do not own a
 * modern import handler yet (customers, addresses, manufacturers, suppliers,
 * combinations, aliases, store contacts).
 *
 * The legacy controller is entirely request/context driven: it reads its inputs
 * through Tools::getValue() and accumulates messages on its public $errors /
 * $warnings / $informations properties. This adapter isolates that coupling so
 * the command handler stays clean, and maps the outcome onto an ImportResult.
 *
 * Nothing in the legacy controller is modified — it is invoked as-is.
 */
final class LegacyImportExecutor
{
    /**
     * Number of rows handled per importByGroups() call. The loop below keeps
     * advancing the offset until the controller reports the import is finished,
     * so this only controls batch granularity, not the total imported.
     */
    private const BATCH_SIZE = 100;

    /**
     * Safety bound to avoid an infinite loop if the controller never reports
     * completion (e.g. an unreadable file).
     */
    private const MAX_ITERATIONS = 100000;

    public function execute(ImportCsvFromFileCommand $command): ImportResult
    {
        $backup = [
            'post' => $_POST,
            'get' => $_GET,
        ];

        $this->resetLegacyStaticState();
        $this->applyRequestParameters($command);

        try {
            // The constructor reads "entity"/"separator" to build the field map,
            // so the request parameters must be applied beforehand.
            $controller = new AdminImportController();
            // The controller's row methods resolve services through its container, which is normally
            // wired by init(). We invoke importByGroups() directly, so inject the container ourselves.
            $this->injectContainer($controller);

            $doneCount = 0;
            $totalCount = 0;
            $offset = 0;
            $moreStep = 0;
            $crossStepsVariables = [];
            $finished = false;
            $iteration = 0;

            do {
                $_POST['offset'] = $offset;
                $_POST['moreStep'] = $moreStep;
                $_POST['crossStepsVars'] = json_encode($crossStepsVariables);

                $results = [];
                $controller->importByGroups(
                    $offset,
                    self::BATCH_SIZE,
                    $results,
                    $command->isValidateOnly(),
                    $moreStep
                );

                if (isset($results['crossStepsVariables'])) {
                    $crossStepsVariables = $results['crossStepsVariables'];
                }
                if (isset($results['totalCount'])) {
                    $totalCount = (int) $results['totalCount'];
                }

                $finished = !empty($results['isFinished']);

                if ($finished && isset($results['oneMoreStep'])) {
                    // The controller requested an additional pass (e.g. linking
                    // accessories): restart from the beginning for that step.
                    $moreStep = (int) $results['oneMoreStep'];
                    $offset = 0;
                    $finished = false;
                } elseif ($finished) {
                    $doneCount = (int) ($results['doneCount'] ?? $offset);
                } else {
                    $offset = (int) ($results['doneCount'] ?? ($offset + self::BATCH_SIZE));
                }
            } while (!$finished && ++$iteration < self::MAX_ITERATIONS);

            return new ImportResult(
                $this->normalizeMessages($controller->errors),
                $this->normalizeMessages($controller->warnings),
                $this->normalizeMessages($controller->informations),
                $doneCount,
                $totalCount
            );
        } finally {
            $_POST = $backup['post'];
            $_GET = $backup['get'];
        }
    }

    /**
     * The legacy controller keeps its column mapping, validators and default values in public static
     * properties that the constructor only adds to (never fully resets). When several entity types are
     * imported in the same process they would otherwise leak into each other (e.g. the store-contact
     * import marking "address1" as a multilang field, which then breaks the address import). Restore the
     * declared defaults before each run.
     */
    private function resetLegacyStaticState(): void
    {
        $defaults = (new ReflectionClass(AdminImportController::class))->getDefaultProperties();

        AdminImportController::$validators = $defaults['validators'];
        AdminImportController::$default_values = $defaults['default_values'];
        AdminImportController::$column_mask = $defaults['column_mask'];
    }

    private function injectContainer(AdminImportController $controller): void
    {
        if (null !== $controller->getContainer()) {
            return;
        }

        $property = new ReflectionProperty(Controller::class, 'container');
        $property->setValue($controller, SymfonyContainer::getInstance());
    }

    /**
     * Populates the request parameters the legacy controller reads through
     * Tools::getValue(). In the test environment Tools::getValue() falls back to
     * the $_POST/$_GET superglobals, which is what the Behat safety net relies on.
     */
    private function applyRequestParameters(ImportCsvFromFileCommand $command): void
    {
        $options = $command->getOptions();

        $parameters = [
            'entity' => $command->getEntityType(),
            'csv' => $command->getFilename(),
            'iso_lang' => $command->getLangIso(),
            'separator' => $options['separator'] ?? ImportSettings::DEFAULT_SEPARATOR,
            'multiple_value_separator' => $options['multiple_value_separator'] ?? ImportSettings::DEFAULT_MULTIVALUE_SEPARATOR,
            'truncate' => !empty($options['truncate']) ? 1 : 0,
            'forceIDs' => !empty($options['forceIDs']) ? 1 : 0,
            'match_ref' => !empty($options['match_ref']) ? 1 : 0,
            'regenerate' => !empty($options['regenerate']) ? 1 : 0,
            'skip' => (int) ($options['skip'] ?? 0),
            'type_value' => $command->getDataMapping(),
            'validateOnly' => $command->isValidateOnly() ? 1 : 0,
        ];

        foreach ($parameters as $key => $value) {
            $_POST[$key] = $value;
            $_GET[$key] = $value;
        }
    }

    /**
     * @param mixed $messages
     *
     * @return string[]
     */
    private function normalizeMessages($messages): array
    {
        if (!is_array($messages)) {
            return [];
        }

        return array_values(array_map(static fn ($message): string => (string) $message, $messages));
    }
}
