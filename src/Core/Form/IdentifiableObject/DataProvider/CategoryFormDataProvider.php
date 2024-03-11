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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\CategoryProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides data for category add/edit category forms
 */
final class CategoryFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int
     */
    private $contextShopRootCategoryId;

    /**
     * @var GroupDataProvider
     */
    private $groupDataProvider;

    /**
     * @var CategoryProvider
     */
    private $categoryProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $contextShopId
     * @param int $contextShopRootCategoryId
     * @param GroupDataProvider $groupDataProvider
     * @param CategoryProvider $categoryProvider
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $contextShopId,
        $contextShopRootCategoryId,
        GroupDataProvider $groupDataProvider,
        CategoryProvider $categoryProvider,
        UrlGeneratorInterface $router
    ) {
        $this->queryBus = $queryBus;
        $this->contextShopId = $contextShopId;
        $this->contextShopRootCategoryId = $contextShopRootCategoryId;
        $this->groupDataProvider = $groupDataProvider;
        $this->categoryProvider = $categoryProvider;
        $this->router = $router;
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
                    'image_path' => $thumbnailImage['path'],
                    'size' => $thumbnailImage['size'],
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
            'meta_keyword' => $editableCategory->getMetaKeywords(),
            'link_rewrite' => $editableCategory->getLinkRewrite(),
            'group_association' => $editableCategory->getGroupAssociationIds(),
            'shop_association' => $editableCategory->getShopAssociationIds(),
            'cover_image' => $coverImages,
            'thumbnail_image' => $thumbnailImages,
            'seo_preview' => $categoryUrl,
            'redirect_option' => $this->extractRedirectOptionData($editableCategory)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $allGroupIds = $this->groupDataProvider->getAllGroupIds();

        return [
            'id_parent' => $this->contextShopRootCategoryId,
            'group_association' => $allGroupIds,
            'shop_association' => $this->contextShopId,
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
