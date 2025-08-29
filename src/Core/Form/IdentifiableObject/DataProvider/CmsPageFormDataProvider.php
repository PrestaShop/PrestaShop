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
