<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.01
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;


use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
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
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function getData($id)
    {
        /** @var EditableCmsPageCategory $editableCmsPageCategory */
        $editableCmsPageCategory = $this->queryBus->handle(new GetCmsPageCategoryForEditing((int) $id));

        return [
            'name' => $editableCmsPageCategory->getLocalisedName(),
            'is_displayed' => $editableCmsPageCategory->isDisplayed(),
            'parent_category' => $editableCmsPageCategory->getParentId()->getValue(),
            'description' => $editableCmsPageCategory->getLocalisedDescription(),
            'meta_title' => $editableCmsPageCategory->getMetaTitle(),
            'meta_description' => $editableCmsPageCategory->getLocalisedMetaDescription(),
            'meta_keywords' => $editableCmsPageCategory->getLocalisedMetaKeywords(),
            'friendly_url' => $editableCmsPageCategory->getLocalisedFriendlyUrl(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return null;
    }
}
