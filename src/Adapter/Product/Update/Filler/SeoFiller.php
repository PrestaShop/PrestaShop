<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

class SeoFiller implements ProductFillerInterface
{
    use LocalizedObjectModelTrait;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param Tools $tools
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        Tools $tools
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tools = $tools;
    }

    /**
     * @param Product $product
     * @param UpdateProductCommand $command
     *
     * @return array
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getRedirectOption()) {
            $updatableProperties = array_merge(
                $updatableProperties,
                $this->fillWithRedirectOption($product, $command->getRedirectOption())
            );
        }

        $localizedMetaDescriptions = $command->getLocalizedMetaDescriptions();
        if (null !== $localizedMetaDescriptions) {
            $this->fillLocalizedValues($product, 'meta_description', $localizedMetaDescriptions, $updatableProperties);
        }

        $localizedMetaTitles = $command->getLocalizedMetaTitles();
        if (null !== $localizedMetaTitles) {
            $this->fillLocalizedValues($product, 'meta_title', $localizedMetaTitles, $updatableProperties);
        }

        $localizedLinkRewrites = $command->getLocalizedLinkRewrites();

        if (null !== $localizedLinkRewrites) {
            foreach ($localizedLinkRewrites as $langId => $linkRewrite) {
                if (!empty($linkRewrite)) {
                    $product->link_rewrite[$langId] = $linkRewrite;
                } elseif (!empty($product->name[$langId])) {
                    // When link rewrite is provided empty, then use product name.
                    $product->link_rewrite[$langId] = $this->tools->linkRewrite($product->name[$langId]);
                } else {
                    continue;
                }

                $updatableProperties['link_rewrite'][] = $langId;
            }
        }

        foreach ($product->link_rewrite as $langId => $linkRewrite) {
            if (!empty($linkRewrite) || empty($product->name[$langId])) {
                continue;
            }

            $product->link_rewrite[$langId] = $this->tools->linkRewrite($product->name[$langId]);

            if (!isset($updatableProperties['link_rewrite'])
                // strict false is important, because array_search could also return 0 as found item index
                || false === array_search($langId, $updatableProperties['link_rewrite'], true)
            ) {
                // we only add updatable property for lang if it is not yet added
                $updatableProperties['link_rewrite'][] = $langId;
            }
        }

        return $updatableProperties;
    }

    /**
     * @param Product $product
     * @param RedirectOption $redirectOption
     *
     * @return string[] updatable properties
     *
     * @throws CategoryNotFoundException
     * @throws CoreException
     */
    private function fillWithRedirectOption(Product $product, RedirectOption $redirectOption): array
    {
        $redirectType = $redirectOption->getRedirectType();
        $redirectTarget = $redirectOption->getRedirectTarget();

        if ($redirectType->isProductType()) {
            $this->productRepository->assertProductExists(new ProductId($redirectTarget->getValue()));
        } elseif ($redirectType->isCategoryType() && !$redirectTarget->isNoTarget()) {
            $this->categoryRepository->assertCategoryExists(new CategoryId($redirectTarget->getValue()));
        }

        $product->redirect_type = $redirectType->getValue();
        $product->id_type_redirected = $redirectTarget->getValue();

        return [
            'redirect_type',
            'id_type_redirected',
        ];
    }
}
