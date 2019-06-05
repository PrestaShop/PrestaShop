<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
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
            'parent_category' => CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            'shop_association' => $this->contextShopIds,
            'is_displayed' => true,
        ];
    }
}
