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

use Language;
use RuntimeException;

class LanguageFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given language :reference with locale :locale exists
     */
    public function createLanguageWithLocale($reference, $locale)
    {
        $languageId = Language::getIdByLocale($locale, true);

        if (false === $languageId) {
            $language = new Language();
            $language->locale = $locale;
            $language->active = true;
            $language->name = $locale;
            $language->is_rtl = false;
            $language->language_code = strtolower($locale);
            $language->iso_code = substr($locale, 0, strpos($locale, '-'));
            $language->add();
        } else {
            $language = new Language($languageId);
        }

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
