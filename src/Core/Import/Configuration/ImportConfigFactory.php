<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    public function buildFromRequest(Request $request)
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
