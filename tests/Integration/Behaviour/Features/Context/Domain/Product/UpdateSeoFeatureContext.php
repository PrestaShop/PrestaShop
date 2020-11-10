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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForRedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForRedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateSeoFeatureContext extends AbstractProductFeatureContext
{
    /**
     * Key for shared storage, where previous search results are stored
     */
    const LATEST_SEARCH_RESULTS_STORAGE_KEY = 'search_for_redirect_product_results';

    /**
     * @When I update product :productReference SEO information with following values:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function updateSeo(string $productReference, TableNode $tableNode): void
    {
        $dataRows = $tableNode->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductSeoCommand($productId);
            $unhandledData = $this->fillUpdateSeoCommand($dataRows, $command);
            Assert::assertEmpty(
                $unhandledData,
                sprintf('Not all provided data was handled in scenario. Unhandled: %s', var_export($unhandledData, true))
            );
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following seo options:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertSeoOptions(string $productReference, TableNode $tableNode): void
    {
        $productSeoOptions = $this->getProductForEditing($productReference)->getProductSeoOptions();
        $dataRows = $tableNode->getRowsHash();

        if (isset($dataRows['redirect_type'])) {
            Assert::assertEquals(
                $dataRows['redirect_type'],
                $productSeoOptions->getRedirectType(),
                'Unexpected redirect_type'
            );
            unset($dataRows['redirect_type']);
        }

        $expectedRedirectTarget = isset($dataRows['redirect_target']) ?
            $this->getSharedStorage()->get($dataRows['redirect_target']) :
            RedirectTarget::NO_TARGET
        ;

        Assert::assertEquals(
            $expectedRedirectTarget,
            $productSeoOptions->getRedirectTargetId(),
            'Unexpected redirect target'
        );
        unset($dataRows['redirect_target']);
    }

    /**
     * @When I update product :productReference localized SEO field :field with a value of :length symbols length
     *
     * @param string $productReference
     * @param string $field
     * @param int $length
     */
    public function updateLocalizedSeoFieldsTooLongValue(string $productReference, string $field, int $length)
    {
        try {
            $command = new UpdateProductSeoCommand($this->getSharedStorage()->get($productReference));
            switch ($field) {
                case 'meta_title':
                    $command->setLocalizedMetaTitles([
                        $this->getDefaultLangId() => PrimitiveUtils::generateRandomString($length),
                    ]);
                    break;
                case 'meta_description':
                    $command->setLocalizedMetaDescriptions([
                        $this->getDefaultLangId() => PrimitiveUtils::generateRandomString($length),
                    ]);
                    break;
                case 'link_rewrite':
                    $command->setLocalizedLinkRewrites([
                        $this->getDefaultLangId() => PrimitiveUtils::generateRandomString($length),
                    ]);
                    break;
                default:
                    throw new RuntimeException(sprintf('Invalid field "%s" provided to scenario', $field));
            }
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should not have a redirect target
     *
     * @param string $productReference
     */
    public function assertHasNoRedirectTargetId(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEquals(
            RedirectTarget::NO_TARGET,
            $productForEditing->getProductSeoOptions()->getRedirectTargetId(),
            'Product "%s" expected to have no redirect target'
        );
    }

    /**
     * @When I search products for SEO redirect option in :iso language by phrase ":searchPhrase" and limit :limit
     *
     * @param string $iso
     * @param string $searchPhrase
     * @param int $limit
     */
    public function searchProductsForRedirect(string $iso, string $searchPhrase, int $limit): void
    {
        $languageId = Language::getIdByIso($iso, true);
        $searchResults = $this->getQueryBus()->handle(new SearchProductsForRedirectOption(
            $searchPhrase,
            $languageId,
            $limit
        ));

        $this->getSharedStorage()->set(self::LATEST_SEARCH_RESULTS_STORAGE_KEY, $searchResults);
    }

    /**
     * @Then search results for product SEO redirect option should be the following:
     *
     * @param TableNode $tableNode
     */
    public function assertSearchResults(TableNode $tableNode): void
    {
        $expectedDataRows = $tableNode->getColumnsHash();
        $searchResults = $this->getSearchResults();
        Assert::assertEquals(count($expectedDataRows), count($searchResults), 'Unexpected search results count');

        foreach ($expectedDataRows as $key => $expectedDataRow) {
            $productForRedirectOption = $searchResults[$key];

            Assert::assertEquals(
                $this->getSharedStorage()->get($expectedDataRow['product']),
                $productForRedirectOption->getProductId(),
                'Unexpected product id in search results'
            );

            Assert::assertEquals(
                $expectedDataRow['name'],
                $productForRedirectOption->getName(),
                'Unexpected product name in search results'
            );

            Assert::assertEquals(
                $expectedDataRow['reference'],
                $productForRedirectOption->getReference(),
                'Unexpected product name in search results'
            );
            //@todo: image is not asserted. How to?
        }
    }

    /**
     * @return ProductForRedirectOption[]
     */
    private function getSearchResults(): array
    {
        return $this->getSharedStorage()->exists(self::LATEST_SEARCH_RESULTS_STORAGE_KEY) ?
            $this->getSharedStorage()->get(self::LATEST_SEARCH_RESULTS_STORAGE_KEY) :
            []
        ;
    }

    /**
     * Fills command with data and returns all additional data that wasn't handled if there is any
     *
     * @param array $dataRows
     * @param UpdateProductSeoCommand $command
     *
     * @return array
     */
    private function fillUpdateSeoCommand(array $dataRows, UpdateProductSeoCommand $command): array
    {
        if (isset($dataRows['meta_title'])) {
            $command->setLocalizedMetaTitles($this->parseLocalizedArray($dataRows['meta_title']));
            unset($dataRows['meta_title']);
        }

        if (isset($dataRows['meta_description'])) {
            $command->setLocalizedMetaDescriptions($this->parseLocalizedArray($dataRows['meta_description']));
            unset($dataRows['meta_description']);
        }

        if (isset($dataRows['link_rewrite'])) {
            $command->setLocalizedLinkRewrites($this->parseLocalizedArray($dataRows['link_rewrite']));
            unset($dataRows['link_rewrite']);
        }

        if (isset($dataRows['redirect_type'], $dataRows['redirect_target'])) {
            if ($this->getSharedStorage()->exists($dataRows['redirect_target'])) {
                $targetId = $this->getSharedStorage()->get($dataRows['redirect_target']);
            }

            $command->setRedirectOption($dataRows['redirect_type'], $targetId ?? 0);
            unset($dataRows['redirect_type'], $dataRows['redirect_target']);
        }

        return $dataRows;
    }
}
