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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Context for updating product SEO properties by using UpdateProductCommand
 *
 * @see UpdateProductCommand
 */
class UpdateSeoFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference SEO information for shop :shopReference with following values:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $tableNode
     */
    public function updateSeoForShop(
        string $productReference,
        string $shopReference,
        TableNode $tableNode
    ): void {
        $this->updateSeo(
            $productReference,
            ShopConstraint::shop(
                $this->getSharedStorage()->get(trim($shopReference))
            ),
            $tableNode
        );
    }

    /**
     * @When I update product :productReference SEO information for all shops with following values:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function updateSeoForAlShops(string $productReference, TableNode $tableNode): void
    {
        $this->updateSeo(
            $productReference,
            ShopConstraint::allShops(),
            $tableNode
        );
    }

    /**
     * @When I update product :productReference SEO information with following values:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function updateSeoForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->updateSeo(
            $productReference,
            ShopConstraint::shop(
                $this->getDefaultShopId()
            ),
            $tableNode
        );
    }

    /**
     * @When I update product :productReference localized SEO field :field with a value of :length symbols length
     *
     * @param string $productReference
     * @param string $field
     * @param int $length
     */
    public function updateLocalizedSeoFieldsTooLongValueForDefaultShop(
        string $productReference,
        string $field,
        int $length
    ): void {
        $this->updateLocalizedSeoFieldsTooLongValue(
            $productReference,
            $field,
            $length,
            ShopConstraint::shop($this->getDefaultShopId())
        );
    }

    /**
     * @param string $productReference
     * @param ShopConstraint $shopConstraint
     * @param TableNode $tableNode
     */
    private function updateSeo(
        string $productReference,
        ShopConstraint $shopConstraint,
        TableNode $tableNode
    ): void {
        $dataRows = $this->localizeByRows($tableNode);
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductCommand($productId, $shopConstraint);
            $unhandledData = $this->fillCommand($dataRows, $command);
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
     * @param string $productReference
     * @param string $field
     * @param int $length
     * @param ShopConstraint $shopConstraint
     */
    private function updateLocalizedSeoFieldsTooLongValue(
        string $productReference,
        string $field,
        int $length,
        ShopConstraint $shopConstraint
    ): void {
        try {
            $command = new UpdateProductCommand(
                $this->getSharedStorage()->get($productReference),
                $shopConstraint
            );
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
     * Fills command with data and returns all additional data that wasn't handled if there is any
     *
     * @param array $dataRows
     * @param UpdateProductCommand $command
     *
     * @return array
     */
    private function fillCommand(array $dataRows, UpdateProductCommand $command): array
    {
        if (isset($dataRows['meta_title'])) {
            $command->setLocalizedMetaTitles($dataRows['meta_title']);
            unset($dataRows['meta_title']);
        }

        if (isset($dataRows['meta_description'])) {
            $command->setLocalizedMetaDescriptions($dataRows['meta_description']);
            unset($dataRows['meta_description']);
        }

        if (isset($dataRows['link_rewrite'])) {
            $command->setLocalizedLinkRewrites($dataRows['link_rewrite']);
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
