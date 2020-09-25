<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Meta;
use RuntimeException;

class MetaFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Then /^meta "([^"]*)" page should be "([^"]*)"$/
     */
    public function assertMetaPageShouldBe($reference, $expectedPageName)
    {
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->page !== $expectedPageName) {
            throw new RuntimeException(sprintf('Expected page name "%s" did not matched given %s', $expectedPageName, $meta->page));
        }
    }

    /**
     * @Given /^meta "([^"]*)" page title for default language should be "([^"]*)"$/
     */
    public function AssertMetaPageTitleForDefaultLanguageShouldBe($reference, $expectedTitle)
    {
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->title[$defaultLanguageId] !== $expectedTitle) {
            throw new RuntimeException(sprintf('Expected title "%s" did not matched given %s for language %s', $expectedTitle, $meta->title[$defaultLanguageId], $defaultLanguageId));
        }
    }

    /**
     * @Given /^meta "([^"]*)" field "([^"]*)" for default language should be "([^"]*)"$/
     */
    public function metaFieldForDefaultLanguageShouldBe($reference, $field, $expectedValue)
    {
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->{$field}[$defaultLanguageId] !== $expectedValue) {
            throw new RuntimeException(sprintf('Expected value "%s" did not matched given "%s" for language %d', $expectedValue, $meta->{$field}[$defaultLanguageId], $defaultLanguageId));
        }
    }
}
