<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

/**
 * Context for product assertions related to SEO properties
 */
class SeoAssertionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference should have following seo options for shops :shopReferences:
     *
     * @param string $productReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    public function assertSeoOptionsForShops(
        string $productReference,
        string $shopReferences,
        TableNode $tableNode
    ): void {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $this->assertSeoOptions(
                $productReference,
                $this->getSharedStorage()->get(trim($shopReference)),
                $tableNode
            );
        }
    }

    /**
     * @Then product :productReference should have following seo options:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertSeoOptionsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertSeoOptions(
            $productReference,
            null,
            $tableNode
        );
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
        Assert::assertNull($productForEditing->getProductSeoOptions()->getRedirectTarget());
    }

    /**
     * @param string $productReference
     * @param int|null $shopId
     * @param TableNode $tableNode
     */
    private function assertSeoOptions(
        string $productReference,
        ?int $shopId,
        TableNode $tableNode
    ): void {
        $productSeoOptions = $this
            ->getProductForEditing($productReference, $shopId)
            ->getProductSeoOptions()
        ;
        $dataRows = $tableNode->getRowsHash();

        $redirectType = $dataRows['redirect_type'] ?? RedirectType::TYPE_PRODUCT_PERMANENT;
        if (isset($dataRows['redirect_type'])) {
            Assert::assertEquals(
                $dataRows['redirect_type'],
                $productSeoOptions->getRedirectType(),
                'Unexpected redirect_type'
            );
            unset($dataRows['redirect_type']);
        }

        $expectedRedirectTarget = !empty($dataRows['redirect_target']) ?
            $this->getSharedStorage()->get($dataRows['redirect_target']) :
            RedirectTarget::NO_TARGET
        ;

        Assert::assertEquals(
            $expectedRedirectTarget,
            $productSeoOptions->getRedirectTargetId(),
            'Unexpected redirect target'
        );
        if ($expectedRedirectTarget) {
            Assert::assertEquals(
                $expectedRedirectTarget,
                $productSeoOptions->getRedirectTarget()->getId(),
                'Unexpected redirect target'
            );
        } else {
            Assert::assertNull(
                $productSeoOptions->getRedirectTarget(),
                'Unexpected redirect target'
            );
        }
        unset($dataRows['redirect_target']);

        if (isset($dataRows['redirect_name'])) {
            Assert::assertEquals(
                $dataRows['redirect_name'],
                $productSeoOptions->getRedirectTarget()->getName(),
                'Unexpected redirect_name'
            );
            unset($dataRows['redirect_name']);
        }

        if (isset($dataRows['redirect_image'])) {
            switch ($redirectType) {
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                    $realImageUrl = $this->getRealCategoryImageUrl($dataRows['redirect_image']);
                    break;
                default:
                    $realImageUrl = $this->getRealImageUrl($dataRows['redirect_image']);
                    break;
            }

            Assert::assertEquals(
                $realImageUrl,
                $productSeoOptions->getRedirectTarget()->getImage(),
                'Unexpected redirect_image'
            );
            unset($dataRows['redirect_image']);
        }
    }
}
