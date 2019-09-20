<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Language;
use RuntimeException;

class LanguageFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given I add a new language :reference with following properties:
     */
    public function addLocale($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $language = new Language();
        $language->name = $data['name'];
        $language->iso_code = $data['iso_code'];
        $language->language_code = $data['language_code'];
        $language->locale = $data['locale'];
        $language->date_format_lite = $data['date_format_lite'];
        $language->date_format_full = $data['date_format_full'];
        $language->is_rtl = (bool) $data['is_rtl'];
        $language->add();

        SharedStorage::getStorage()->set($reference, $language);
    }

    /**
     * @Then language :reference should be :locale
     */
    public function assertLanguageLocale($reference, $locale)
    {
        /** @var Language $language */
        $language = SharedStorage::getStorage()->get($reference);

        if ($language->locale !== $locale) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" iso code, but "%s" was expected.',
                $reference,
                $language->locale,
                $locale
            ));
        }
    }
}
