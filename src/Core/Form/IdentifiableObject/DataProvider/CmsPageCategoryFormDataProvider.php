<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.01
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;


use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\DTO\EditableCmsPageCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;

final class CmsPageCategoryFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param CommandBusInterface $queryBus
     * @param array $contextShopIds
     */
    public function __construct(CommandBusInterface $queryBus, array $contextShopIds)
    {
        $this->queryBus = $queryBus;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function getData($id)
    {
        /** @var EditableCmsPageCategory $editableCmsPageCategory */
        $editableCmsPageCategory = $this->queryBus->handle(new GetCmsPageCategoryForEditing($id));

        return [
            'name' => $editableCmsPageCategory->getLocalisedName(),
            'is_displayed' => $editableCmsPageCategory->isDisplayed(),
            'parent_category' => $editableCmsPageCategory->getParentId()->getValue(),
            'description' => $editableCmsPageCategory->getLocalisedDescription(),
            'meta_title' => $editableCmsPageCategory->getMetaTitle(),
            'meta_description' => $editableCmsPageCategory->getLocalisedMetaDescription(),
            'meta_keywords' => $editableCmsPageCategory->getLocalisedMetaKeywords(),
            'friendly_url' => $editableCmsPageCategory->getLocalisedFriendlyUrl(),
            'shop_association' => $editableCmsPageCategory->getShopIds(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'parent_category' => CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
            'shop_association' => $this->contextShopIds,
        ];
    }
}
