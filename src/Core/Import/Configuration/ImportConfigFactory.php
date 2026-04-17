<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

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
            $request->request->getInt('entity', $request->getSession()->get('entity', 0)),
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
}
