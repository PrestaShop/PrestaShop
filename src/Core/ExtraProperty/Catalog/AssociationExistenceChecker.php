<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\AssociationEntryParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Checks the association entries of an extra property definition against the catalogs and
 * reports the targets that do not (yet) exist as translated, NON-BLOCKING warnings.
 *
 * Pointing to a form/grid/API operation the catalogs do not know is a supported manual
 * override (the catalogs are best-effort detections), so an unknown target must never fail
 * the save — the warnings simply tell the user what will happen until the target exists.
 *
 * Entry syntax is validated upstream (the row form types + the definition VO), so
 * unparseable entries are silently skipped here.
 *
 * Defined in app/config/admin/services.yml ONLY: ApiEndpointCatalog needs the OpenApi services
 * that exist solely in the admin kernel (see its docblock).
 */
class AssociationExistenceChecker
{
    public function __construct(
        private readonly FormCatalog $formCatalog,
        private readonly GridCatalog $gridCatalog,
        private readonly ApiEndpointCatalog $apiEndpointCatalog,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param list<string>|null $forms associated_forms entries ("formId[:path[:before|after]]")
     * @param list<string>|null $grids associated_grids entries ("gridId[:columnId[:before|after]]")
     * @param list<string>|null $apis associated_apis entries ("uriPath[:METHOD[,METHOD...]]")
     *
     * @return list<string> translated warnings (empty when every target exists)
     */
    public function check(?array $forms, ?array $grids, ?array $apis): array
    {
        return array_merge(
            $this->checkForms($forms ?? []),
            $this->checkGrids($grids ?? []),
            $this->checkApis($apis ?? [])
        );
    }

    /**
     * @param list<string> $entries
     *
     * @return list<string>
     */
    private function checkForms(array $entries): array
    {
        $warnings = [];
        foreach ($entries as $entry) {
            $formId = AssociationEntryParser::parseFormEntry($entry)['formId'];
            if ('' === $formId || $this->formCatalog->has($formId)) {
                continue;
            }

            $warnings[] = $this->translator->trans(
                'No back-office form with id "%formId%" was detected — the placement will be ignored until such a form exists.',
                ['%formId%' => $formId],
                'Admin.Advparameters.Notification'
            );
        }

        return $warnings;
    }

    /**
     * @param list<string> $entries
     *
     * @return list<string>
     */
    private function checkGrids(array $entries): array
    {
        $warnings = [];
        foreach ($entries as $entry) {
            ['gridId' => $gridId, 'columnId' => $columnId] = AssociationEntryParser::parseGridEntry($entry);
            if ('' === $gridId) {
                continue;
            }

            $grid = $this->gridCatalog->get($gridId);
            if (null === $grid) {
                $warnings[] = $this->translator->trans(
                    'No back-office grid with id "%gridId%" was detected — the placement will be ignored until such a grid exists.',
                    ['%gridId%' => $gridId],
                    'Admin.Advparameters.Notification'
                );

                continue;
            }

            if (null !== $columnId && !$this->gridHasColumn($grid, $columnId)) {
                $warnings[] = $this->translator->trans(
                    'The grid "%gridId%" has no column with id "%columnId%" — the extra column will be appended at the default position instead.',
                    ['%gridId%' => $gridId, '%columnId%' => $columnId],
                    'Admin.Advparameters.Notification'
                );
            }
        }

        return $warnings;
    }

    /**
     * @param list<string> $entries
     *
     * @return list<string>
     */
    private function checkApis(array $entries): array
    {
        $warnings = [];
        foreach ($entries as $entry) {
            // Skip entries whose RAW path part is empty (e.g. ":GET"): parseApiEntry() would
            // normalize them to "/", which is not what the user targeted — same guard as
            // AssociationEntryParser::assertValidApiEntry().
            $colonPos = strpos($entry, ':');
            if ('' === trim(false !== $colonPos ? substr($entry, 0, $colonPos) : $entry)) {
                continue;
            }

            $path = AssociationEntryParser::parseApiEntry($entry)['path'];
            if ($this->apiEndpointCatalog->hasUriTemplate($path)) {
                continue;
            }

            $warnings[] = $this->translator->trans(
                'No Admin API operation with URI template "%path%" was detected — the property will not be exposed until such an endpoint exists.',
                ['%path%' => $path],
                'Admin.Advparameters.Notification'
            );
        }

        return $warnings;
    }

    /**
     * @param array{columns: list<array{id: string}>} $grid
     */
    private function gridHasColumn(array $grid, string $columnId): bool
    {
        foreach ($grid['columns'] as $column) {
            if ($column['id'] === $columnId) {
                return true;
            }
        }

        return false;
    }
}
