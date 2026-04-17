<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

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
            'parent_category' => CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            'shop_association' => $this->contextShopIds,
            'is_displayed' => true,
        ];
    }
}
