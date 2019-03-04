<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Group\Query\GetDefaultGroups;
use PrestaShop\PrestaShop\Core\Domain\Group\QueryResult\DefaultGroups;

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
     * @param CommandBusInterface $queryBus
     * @param int $contextShopId
     * @param int $contextShopRootCategoryId
     */
    public function __construct(CommandBusInterface $queryBus, $contextShopId, $contextShopRootCategoryId)
    {
        $this->queryBus = $queryBus;
        $this->contextShopId = $contextShopId;
        $this->contextShopRootCategoryId = $contextShopRootCategoryId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($categoryId)
    {
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->queryBus->handle(new GetCategoryForEditing($categoryId));

        return [
            'name' => $editableCategory->getName(),
            'active' => $editableCategory->isActive(),
            'id_parent' => $editableCategory->getParentId(),
            'description' => $editableCategory->getDescription(),
            'meta_title' => $editableCategory->getMetaTitle(),
            'meta_description' => $editableCategory->getMetaDescription(),
            'meta_keyword' => $editableCategory->getMetaKeywords(),
            'link_rewrite' => $editableCategory->getLinkRewrite(),
            'group_association' => $editableCategory->getGroupAssociationIds(),
            'shop_association' => $editableCategory->getShopAssociationIds(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->queryBus->handle(new GetDefaultGroups());

        return [
            'id_parent' => $this->contextShopRootCategoryId,
            'group_association' => [
                $defaultGroups->getVisitorsGroup()->getGroupId()->getValue(),
                $defaultGroups->getGuestsGroup()->getGroupId()->getValue(),
                $defaultGroups->getCustomersGroup()->getGroupId()->getValue(),
            ],
            'shop_association' => [
                $this->contextShopId,
            ],
            'active' => true,
        ];
    }
}
