<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportTypeException;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportConfigFactory describes an import configuration factory.
 */
final class ImportConfigFactory implements ImportConfigFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildFromRequest(Request $request): ImportConfigInterface
    {
        $separator = $request->request->get(
            'separator',
            $request->getSession()->get('separator', ImportSettings::DEFAULT_SEPARATOR)
        );

        $multivalueSeparator = $request->request->get(
            'multiple_value_separator',
            $request->getSession()->get('multiple_value_separator', ImportSettings::DEFAULT_MULTIVALUE_SEPARATOR)
        );

        return new ImportConfig(
            $request->request->get('csv', $request->getSession()->get('csv')),
            $this->resolveEntityType($request),
            $request->request->get('iso_lang', $request->getSession()->get('iso_lang')),
            $separator,
            $multivalueSeparator,
            $request->request->getBoolean('truncate', $request->getSession()->get('truncate', false)),
            $request->request->getBoolean('regenerate', $request->getSession()->get('regenerate', false)),
            $request->request->getBoolean('match_ref', $request->getSession()->get('match_ref', false)),
            $request->request->getBoolean('forceIDs', $request->getSession()->get('forceIDs', false)),
            $request->request->getBoolean('sendemail', $request->getSession()->get('sendemail', true)),
            $request->request->getInt('skip', 0)
        );
    }

    /**
     * Resolve which entity the import page should start on.
     *
     * WHY: every importable grid links here through a LinkGridAction carrying an
     * "import_type" query parameter naming its own entity, and ImportType converts such a
     * name back to an entity type. Only this hop was missing, so the deep link had no effect.
     * A submitted form takes precedence over it, otherwise an "import_type" left over in the
     * URL of a POST would silently override the entity the user picked in the drop-down.
     * An unrecognised name is ignored rather than fatal, because it comes from the query string.
     *
     * @param Request $request
     *
     * @return int
     */
    private function resolveEntityType(Request $request): int
    {
        if ($request->request->has('entity')) {
            return $request->request->getInt('entity');
        }

        $importType = (string) $request->query->get('import_type', '');

        if ('' !== $importType) {
            try {
                return Entity::getFromName($importType);
            } catch (NotSupportedImportTypeException $e) {
                // Unknown entity name, fall back to the session value below.
            }
        }

        return (int) $request->getSession()->get('entity', 0);
    }
}
