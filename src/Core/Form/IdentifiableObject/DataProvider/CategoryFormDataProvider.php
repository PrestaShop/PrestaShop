<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\CategoryProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use Symfony\Component\Routing\Router;

/**
 * Provides data for category add/edit category forms
 */
final class CategoryFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly GroupDataProvider $groupDataProvider,
        private readonly CategoryProvider $categoryProvider,
        private readonly Router $router,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($categoryId)
    {
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->queryBus->handle(new GetCategoryForEditing($categoryId));

        $coverImages = $thumbnailImages = [];
        $categoryId = (int) $categoryId;
        $categoryUrl = $this->categoryProvider->getUrl($categoryId, '{friendly-url}');
        $coverImage = $editableCategory->getCoverImage();
        if ($coverImage) {
            $coverImages[] = [
                'size' => $coverImage['size'],
                'image_path' => $coverImage['path'],
                'delete_path' => $this->router->generate(
                    'admin_categories_delete_cover_image',
                    [
                        'categoryId' => $categoryId,
                    ]
                ),
            ];
        }
        $thumbnailImage = $editableCategory->getThumbnailImage();
        if ($thumbnailImage) {
            $thumbnailImages[] =
                [
                    'size' => $thumbnailImage['size'],
                    'image_path' => $thumbnailImage['path'],
                    'delete_path' => $this->router->generate(
                        'admin_categories_delete_thumbnail_image',
                        [
                            'categoryId' => $categoryId,
                        ]
                    ),
                ];
        }

        return [
            'name' => $editableCategory->getName(),
            'active' => $editableCategory->isActive(),
            'id_parent' => $editableCategory->getParentId(),
            'description' => $editableCategory->getDescription(),
            'additional_description' => $editableCategory->getAdditionalDescription(),
            'meta_title' => $editableCategory->getMetaTitle(),
            'meta_description' => $editableCategory->getMetaDescription(),
            'link_rewrite' => $editableCategory->getLinkRewrite(),
            'group_association' => $editableCategory->getGroupAssociationIds(),
            'shop_association' => $editableCategory->getShopAssociationIds(),
            'cover_image' => $coverImages,
            'thumbnail_image' => $thumbnailImages,
            'seo_preview' => $categoryUrl,
            'redirect_option' => $this->extractRedirectOptionData($editableCategory),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $allGroupIds = $this->groupDataProvider->getAllGroupIds();

        return [
            'id_parent' => $this->shopContext->getCategoryId(),
            'group_association' => $allGroupIds,
            'shop_association' => $this->shopContext->getAssociatedShopIds(),
            'active' => true,
            'seo_preview' => $this->categoryProvider->getUrl(0, '{friendly-url}'),
        ];
    }

    private function extractRedirectOptionData(EditableCategory $editableCategory): array
    {
        // It is important to return null when nothing is selected this way the transformer and therefore
        // the form field have no value to try and display
        $redirectTarget = null;
        if (null !== $editableCategory->getRedirectTarget()) {
            $redirectTarget = [
                'id' => $editableCategory->getRedirectTarget()->getId(),
                'name' => $editableCategory->getRedirectTarget()->getName(),
                'image' => $editableCategory->getRedirectTarget()->getImage(),
            ];
        }

        return [
            'type' => $editableCategory->getRedirectType(),
            'target' => $redirectTarget,
        ];
    }
}
