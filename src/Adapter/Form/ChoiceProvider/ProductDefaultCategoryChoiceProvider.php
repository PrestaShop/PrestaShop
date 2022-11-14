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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Category\NameBuilder\CategoryDisplayNameBuilder;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Product;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDefaultCategoryChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CategoryId
     */
    private $defaultCategoryId;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryDisplayNameBuilder
     */
    private $categoryDisplayNameBuilder;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param int $homeCategoryId
     * @param CategoryRepository $categoryRepository
     * @param CategoryDisplayNameBuilder $categoryDisplayNameBuilder
     * @param int $shopId
     * @param int $languageId
     */
    public function __construct(
        int $homeCategoryId,
        CategoryRepository $categoryRepository,
        CategoryDisplayNameBuilder $categoryDisplayNameBuilder,
        int $shopId,
        int $languageId
    ) {
        $this->defaultCategoryId = new CategoryId($homeCategoryId);
        $this->categoryRepository = $categoryRepository;
        $this->categoryDisplayNameBuilder = $categoryDisplayNameBuilder;
        $this->shopId = new ShopId($shopId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);

        if (!$options['product_id']) {
            $category = $this->categoryRepository->get($this->defaultCategoryId);
            $displayName = $this->getDisplayName(
                $this->defaultCategoryId,
                $category->getName($this->languageId->getValue())
            );
            // if no product is provided, then default category can only be home
            return [$displayName => $this->defaultCategoryId->getValue()];
        }

        return $this->getChoicesForExistingProduct($options['product_id']);
    }

    /**
     * @param int $productId
     *
     * @return array<string, int>
     */
    private function getChoicesForExistingProduct(int $productId): array
    {
        $productCategories = Product::getProductCategoriesFull($productId);
        $choices = [];

        foreach ($productCategories as $categoryId => $productCategory) {
            $categoryIdVo = new CategoryId((int) $categoryId);
            $displayName = $this->getDisplayName($categoryIdVo, $productCategory['name']);
            $choices[$displayName] = $categoryIdVo->getValue();
        }

        return $choices;
    }

    /**
     * @param CategoryId $categoryId
     * @param string $categoryName
     *
     * @return string
     */
    private function getDisplayName(CategoryId $categoryId, string $categoryName): string
    {
        return $this->categoryDisplayNameBuilder->build(
            $categoryName,
            $this->shopId,
            $this->languageId,
            $categoryId
        );
    }

    /**
     * Returns resolved options
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(['product_id' => null]);
        $resolver->setAllowedTypes('product_id', ['int', 'null']);

        return $resolver->resolve($options);
    }
}
