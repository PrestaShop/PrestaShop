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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags as LocalizedTagsDto;
use RuntimeException;

class UpdateTagsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference tags with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductTags(string $productReference, TableNode $table): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $data = $this->localizeByRows($table);

        $localizedTagsList = [];
        foreach ($data['tags'] as $langId => $localizedTagString) {
            $localizedTagsList[$langId] = explode(',', $localizedTagString);
        }

        try {
            $this->getCommandBus()->handle(new SetProductTagsCommand($productId, $localizedTagsList));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all product :productReference tags
     *
     * @param string $productReference
     */
    public function removeAllTags(string $productReference)
    {
        $productId = $this->getSharedStorage()->get($productReference);

        $this->getCommandBus()->handle(new RemoveAllProductTagsCommand($productId));
    }

    /**
     * Product tags differs from other localized properties, because each locale can have an array of tags
     * (whereas common property will have one value per language)
     * This is why it needs some additional parsing
     *
     * @param array $localizedTagStrings key value pairs where key is language id and value is string representation of array separated by comma
     *                                   e.g. [1 => 'hello,goodbye', 2 => 'bonjour,Au revoir']
     * @param LocalizedTagsDto[] $actualLocalizedTagsList
     */
    public static function assertLocalizedTags(array $localizedTagStrings, array $actualLocalizedTagsList)
    {
        foreach ($localizedTagStrings as $langId => $tagsString) {
            $langIso = Language::getIsoById($langId);

            if (empty($tagsString)) {
                // if tags string is empty, then we should not have any actual value in this language
                foreach ($actualLocalizedTagsList as $actualLocalizedTags) {
                    if ($actualLocalizedTags->getLanguageId() === $langId) {
                        throw new RuntimeException(sprintf(
                                'Expected no tags in %s language, but got "%s"',
                                $langIso,
                                var_export($actualLocalizedTags->getTags(), true))
                        );
                    }
                }

                // if above code passed it means tags in this lang is empty as expected and we can continue
                continue;
            }

            // convert filled tags to array
            $expectedTags = array_map('trim', explode(',', $tagsString));
            $valueInLangExists = false;
            foreach ($actualLocalizedTagsList as $actualLocalizedTags) {
                if ($actualLocalizedTags->getLanguageId() !== $langId) {
                    continue;
                }

                Assert::assertEquals(
                    $expectedTags,
                    $actualLocalizedTags->getTags(),
                    sprintf(
                        'Expected tags in "%s" language was "%s", but got "%s"',
                        $langIso,
                        var_export($expectedTags, true),
                        var_export($actualLocalizedTags->getTags(), true)
                    )
                );
                $valueInLangExists = true;
            }

            // All empty values have ben filtered out above,
            // so if this lang value doesn't exist, it means it didn't meet the expectations
            if (!$valueInLangExists) {
                throw new RuntimeException(sprintf(
                    'Expected localized tags value "%s" is not set in %s language',
                    var_export($expectedTags, true),
                    $langIso
                ));
            }
        }
    }
}
