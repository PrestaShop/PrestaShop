<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult\EditableCmsPage;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * {@inheritdoc}
 */
class CmsPageFormDataProvider implements FormDataProviderInterface
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
     * @throws CmsPageException
     */
    public function getData($cmsPageId)
    {
        /** @var EditableCmsPage $editableCmsPage */
        $editableCmsPage = $this->queryBus->handle(new GetCmsPageForEditing($cmsPageId));

        return [
            'page_category_id' => $editableCmsPage->getCmsPageCategoryId()->getValue(),
            'title' => $editableCmsPage->getLocalizedTitle(),
            'meta_title' => $editableCmsPage->getLocalizedMetaTitle(),
            'meta_description' => $editableCmsPage->getLocalizedMetaDescription(),
            'friendly_url' => $editableCmsPage->getLocalizedFriendlyUrl(),
            'content' => $editableCmsPage->getLocalizedContent(),
            'is_indexed_for_search' => $editableCmsPage->isIndexedForSearch(),
            'is_displayed' => $editableCmsPage->isDisplayed(),
            'shop_association' => $editableCmsPage->getShopAssociation(),
        ];
    }

    /**
     * Get default form data.
     *
     * @return mixed
     */
    public function getDefaultData()
    {
        return [
            'page_category_id' => CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            'shop_association' => $this->contextShopIds,
            'is_indexed_for_search' => false,
            'is_displayed' => false,
        ];
    }
}
